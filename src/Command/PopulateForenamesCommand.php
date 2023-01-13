<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Surname;
use App\Repository\ForenameRepository;
use App\Service\SlugGenerator;
use BorderCloud\SPARQL\SparqlClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class PopulateForenamesCommand extends Command
{
    protected static $defaultName = 'app:populate-forename';

    protected $slugGenerator;
    private $em;
    private $forenameRepository;

    public function __construct(SlugGenerator $slugGenerator, EntityManagerInterface $em, ForenameRepository $forenameRepository)
    {
        $this->slugGenerator = $slugGenerator;
        $this->em = $em;
        $this->forenameRepository = $forenameRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Load from TSV File');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Chargement d'un fichier des prénoms");
        $io->writeln("Les fichiers correspondent au résultat SparQL des deux requêtes suivantes téléchargé au format TSV puis déposé dans var/sparql : ");
        $io->title("Requête 1");
        $io->writeln("SELECT distinct ?forename (STR(?forenameLabelRaw) as ?forenameLabel)
WHERE
{
  ?forename wdt:P31/wdt:P279* wd:Q202444.
  ?forename wdt:P1705 ?forenameLabelRaw
}");
        $io->title("Requête 2");
        $io->writeln("SELECT distinct ?forename ?forenameLabel
WHERE
{
  ?forename wdt:P31/wdt:P279*  wd:Q202444 .
  MINUS
  {
    ?forename wdt:P1705 ?dummy
  } .
  SERVICE wikibase:label { bd:serviceParam wikibase:language \"[AUTO_LANGUAGE],nl,fr,en,de,it,es,no,pt\". }
} 
        ");


        $io->writeLn("");
        $io->writeln("<info>Sélection du fichier contenant  les résultats : </info>");
        $io->writeLn("");

        $helper = $this->getHelper('question');
        $finder = new Finder();
        $directory_files = __DIR__ . "/../../var/sparql/";
        $finder->files()->in($directory_files);
        $choices = [];
        foreach ($finder as $file) {
            $choices[] = $directory_files . "/" . $file->getFilename();
        }
        $question = new ChoiceQuestion(
            'Choisir le fichier contenant les résultats de la requête SPARQL',
            $choices
        );
        $question->setErrorMessage('Fichier invalide.');
        $filename = $helper->ask($input, $output, $question);


        $start = microtime(true);
        $handle = @fopen($filename, "r");

        $doneSession = [];

        if ($handle) {
            $lineNumber = 0;
            $countImports = 0;
            while (($line = fgets($handle, 4096)) !== false) {
                if ($lineNumber > 0) {
                    $line = chop($line);
                    $values = preg_split("/\t/", $line);

                    if (!preg_match("/^Q\d*$/", $values[1])) {
                        $label = $values[1];
                        $forename = new Forename($this->slugGenerator);
                        $forename->setWikidata($values[0]);
                        $forename->setLabel($label);

                        $labelSearch = strtoupper(iconv('utf-8', 'us-ascii//TRANSLIT', $label));
                        if (preg_match("/\?/", $labelSearch)) {
                            # Convert to latin1 does not work, we skip
                        } else {
                            if (isset($doneSession[$labelSearch])) {

                                $doneSession = [];
                                $this->em->flush();
                            }

                            $previousForename = $this->forenameRepository->findOneBy(["label" => $label]);

                            if (!is_null($previousForename)) {
                                $previousForename->appendWikidata($forename->getWikidata());
                                $this->em->persist($previousForename);
                            }
                            else {
                                if ($forename->getLabelsLength() < 250) {
                                    $this->em->persist($forename);
                                    $doneSession[$labelSearch] = $forename;
                                    $countImports++;
                                } else {
                                    print "Skip ".$forename->getLabel()."\n";
                                }
                            }
                        }
                    }
                }

                if (($countImports % 5000) == 0) {

                    $duration = microtime(true) - $start;
                    print "Import : $countImports - start flush [$duration]\n";
                    $start = microtime(true);

                    $duration = microtime(true) - $start;
                    print "Import : $countImports - end flush [$duration]\n";
                    $start = microtime(true);
                }
                $lineNumber++;
            }

            fclose($handle);
        }
        $this->em->flush();

        return 1;
    }
}
