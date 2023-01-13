<?php

namespace App\Command;

use App\Entity\Quizz;
use App\Entity\QuizzCategory;
use App\Entity\Result;
use App\Repository\ResultRepository;
use App\Service\PseudonameFinder;
use App\Service\SlugGenerator;
use App\Service\WikiClient;
use BorderCloud\SPARQL\SparqlClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateQuizzesCommand extends Command
{
    protected static $defaultName = 'app:create-quizzes';

    private $wikiClient;
    private $em;
    private $pseudonameFinder;
    private $slugGenerator;

    public function __construct(WikiClient $wikiClient, EntityManagerInterface $em, PseudonameFinder $pseudonameFinder, SlugGenerator $slugGenerator)
    {
        $this->wikiClient = $wikiClient;
        $this->em = $em;
        $this->pseudonameFinder = $pseudonameFinder;
        $this->slugGenerator = $slugGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create quizzes for a list of Q');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $quizzCategories = $this->em->getRepository(QuizzCategory::class)->findAll();
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Please select your favorite color (defaults to red)',
            $quizzCategories,
            0
        );
        $categoryName = $helper->ask($input, $output, $question);

        $quizzCategory = $this->em->getRepository(QuizzCategory::class)->findOneBy(["name" => $categoryName]);

        $client = new SparqlClient();
        $client->setEndpointRead("https://query.wikidata.org/sparql");

        #Personnes décédées en 2018 classées par nombre de liens de sites
        $q = "select ?person ?sitelinks where {
        ?person wdt:P31 wd:Q5 .
        ?person wdt:P106 wd:" . $quizzCategory->getWikidataOccupation() . " .
        ?person wikibase:sitelinks ?sitelinks.
        } order by desc(?sitelinks) limit 100";
        //

        $rows = $client->query($q, 'rows');
        $err = $client->getErrors();
        if ($err) {
            print_r($err);
            throw new Exception(print_r($err, true));
        }

        foreach ($rows["result"]["rows"] as $row) {
            $wikidata = $this->wikiClient->cleanQ($row["person"]);

            $quizz = $this->em->getRepository(Quizz::class)->findOneBy(["wikidata" => $wikidata, "quizzCategory" => $quizzCategory
            ]);

            if (!is_null($quizz)) {
                $io->writeln("<error>Quizz already exists</error>");
            } else {
                $informations = $this->wikiClient->getWikidataJson($wikidata);
                $label = $this->wikiClient->extractLabel($informations, $wikidata);

                if (strlen($label) < 10) {
                    $io->writeln("<error>Label is too short</error>");
                } else {
                    $quizz = new Quizz();
                    $quizz->setQuizzCategory($quizzCategory);
                    $quizz->setWikidata($wikidata);
                    $io->title("$label (".$row["sitelinks"].")");

                    $io->writeln("<info>Building results</info>");
                    $result = new Result($this->slugGenerator);
                    $result->setSearch($label);
                    $count = 0;
                    for ($i = 4; $i <= strlen($label) - 4; $i++) {
                        $start = microtime(true);
                        $anagrams = $this->pseudonameFinder->search($result->getSlug(), $i);
                        $duration = microtime(true) - $start;
                        $resultStep = $result->addAnagrams($anagrams, $i, $duration);
                        $this->em->persist($resultStep);
                        $io->writeln("Search for $i => ".sizeof($resultStep->getAnagrams()));
                        $count += sizeof($resultStep->getAnagrams());
                    }
                    $io->writeln("$count anagrams found\n\n");
                    $result->setStatus(Result::STATUS_FINISH);
                    $this->em->persist($result);

                    $io->writeln("<info>Set answer</info>");
                    $quizz->setAnswer($label);
                    $description = $this->wikiClient->extractDescription($informations, $wikidata);
                    $quizz->setQuestion($description);

                    $io->writeln("<info>Downloading image</info>");
                    $commonsImageName = $this->wikiClient->extractImageName($informations, $wikidata);
                    if (!is_null($commonsImageName)) {
                        $quizz->setCommonsFilename($commonsImageName);
                    }

                    try {
                        $localImageName = $this->wikiClient->getThumbnailUrl($informations, $wikidata);
                        $quizz->setImage($localImageName);
                    } catch (\Exception $e) {
                        $io->writeln("<error>Error download</error>");
                    }

                    $this->em->persist($quizz);
                    $this->em->flush();
                }
            }

        }
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
