<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Surname;
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

class PopulateSurnamesCommand extends Command
{
    protected static $defaultName = 'app:populate-surname';

    protected $slugGenerator;
    private $em;

    public function __construct(SlugGenerator $slugGenerator, EntityManagerInterface $em)
    {
        $this->slugGenerator = $slugGenerator;
        $this->em = $em;
        parent::__construct();
    }
    protected function configure()
    {
        $this
            ->setDescription('Load from TSV File')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Chargement d'un fichier de noms de famille");
        $io->writeln("Les fichiers correspondent au résultat SparQL de la requête suivante : ");
        $io->writeln("SELECT DISTINCT ?item ?itemLabel ?language
WHERE 
{
  ?item wdt:P31 wd:Q101352.
  ?item wdt:P1705 ?itemLabel.
  BIND (LANG(?itemLabel) AS ?language)
}
");

        $io->writeLn("");
        $io->writeln("<info>Sélection du fichier contenant  les ISBN : </info>");
        $io->writeLn("");


        $helper = $this->getHelper('question');
        $finder = new Finder();
        $directory_files = __DIR__."/../../var/sparql/";
        $finder->files()->in($directory_files );
        $choices = [];
        foreach ($finder as $file) {
            $choices[] = $directory_files."/".$file->getFilename();
        }
        $question = new ChoiceQuestion(
            'Choisir le fichier contenant les résultats de la requête SPARQL',
            $choices
        );
        $question->setErrorMessage('Fichier invalide.');
        $filename = $helper->ask($input, $output, $question);

        $start = microtime(true);
        $handle = @fopen($filename, "r");

        if ($handle) {
            $lineNumber = 0;
            while (($line = fgets($handle, 4096)) !== false) {
                if ($lineNumber > 0) {
                    $values = preg_split("/\t/", $line);
                    $surname = new Surname($this->slugGenerator);
                    $surname->setLabel($values[1]);
                    $surname->setWikidata($values[0]);
                    $surname->setLanguage($values[2]);

                    if ($surname->getLabelsLength() < 250) {
                        $this->em->persist($surname);
                    }
                }
                $lineNumber++;
                if ( ($lineNumber % 5000) == 0) {
                    $duration = microtime(true) - $start;
                    print "Ligne : $lineNumber - start flush [$duration]\n";
                    $start = microtime(true);
                    $this->em->flush();
                    $duration = microtime(true) - $start;
                    print "Ligne : $lineNumber - end flush [$duration]\n";
                    $start = microtime(true);
                }
            }

            fclose($handle);
        }

        return 1;
    }
}
