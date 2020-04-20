<?php

namespace App\Command;

use App\Entity\Quizz;
use App\Service\WikiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateQuizzesCommand extends Command
{
    protected static $defaultName = 'app:create-quizzes';

    private $wikiClient;
    private $em;

    public function __construct(string $name = null, WikiClient $wikiClient, EntityManagerInterface $em)
    {
        $this->wikiClient = $wikiClient;
        $this->em = $em;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Create quizzes for a list of Q')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $Q = ["Q9960", "Q2831", "Q882", "Q4617", "Q5577", "Q5284", "Q303", "Q4612", "Q1203", "Q1744", "Q78516", "Q5608", "Q392", "Q34086", "Q23530", "Q8877", "Q7742", "Q83338", "Q1779", "Q13909", "Q1362169", "Q7546", "Q12897", "Q2685", "Q5105", "Q16397", "Q32927", "Q35332", "Q30449", "Q2599", "Q11637", "Q1631", "Q83287", "Q205707", "Q36844", "Q34424", "Q44301", "Q82110", "Q1276", "Q38111", "Q4573", "Q39829", "Q34389", "Q42786", "Q12881", "Q5383", "Q10520", "Q503706", "Q44461", "Q43203", "Q36949", "Q37001", "Q317521", "Q2263", "Q37459", "Q23844", "Q40912", "Q6107", "Q39792", "Q40096", "Q23543", "Q38222", "Q19794", "Q23359", "Q26876", "Q40504", "Q40523", "Q41173", "Q41594", "Q873", "Q37876", "Q23880", "Q129817", "Q43252", "Q102124", "Q3772", "Q188500", "Q83158", "Q32522", "Q42493", "Q55800", "Q284636", "Q40791", "Q42775", "Q2643", "Q2757", "Q48337", "Q159577", "Q80966", "Q2680", "Q132964", "Q80938", "Q512", "Q42574", "Q43247", "Q43416", "Q56016", "Q3099714", "Q40531", "Q40572"];

        foreach ($Q as $oneQ) {
            $quizz = $this->em->getRepository(Quizz::class)->findOneBy(["wikidata" => $oneQ
            ]);

            if (!is_null($quizz)) {
                print $oneQ."\n";
                $informations = $this->wikiClient->getWikidataJson($oneQ);
                $commonsImageName = $this->wikiClient->extractImageName($informations, $oneQ);
                $quizz->setCommonsFilename($commonsImageName);

                $this->em->persist($quizz);
                $this->em->flush();
            } else {
                $quizz = new Quizz();
                $quizz->setWikidata($oneQ);
                $this->em->persist($quizz);
                $this->em->flush();

                $informations = $this->wikiClient->getWikidataJson($oneQ);
                $label = $this->wikiClient->extractLabel($informations, $oneQ);
                $quizz->setAnswer($label);

                $description = $this->wikiClient->extractDescription($informations, $oneQ);
                $quizz->setQuestion($description);

                $localImageName = $this->wikiClient->getThumbnailUrl($informations, $oneQ);
                $quizz->setImage($localImageName);
                $io->writeln($oneQ);

                $this->em->persist($quizz);
                $this->em->flush();
            }


        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
