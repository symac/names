<?php

namespace App\Command;

use App\Entity\Forename;
use App\Repository\ForenameRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class GetGendersCommand extends Command
{
    protected static $defaultName = 'app:get-genders';

    protected $forenameRepository;
    private $em;

    public function __construct(EntityManagerInterface $em, ForenameRepository $forenameRepository)
    {
        $this->forenameRepository = $forenameRepository;
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Load genders from TSV File');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Chargement d'un fichier des prénoms");
        $io->writeln("Les fichiers correspondent au résultat SparQL des deux requêtes suivantes : ");
        $io->title("Requête :");
        $io->writeln("SELECT DISTINCT ?forename ?forenameType 
WHERE 
{
  ?forename wdt:P31 ?forenameType. 
  {?forename wdt:P31 wd:Q3409032} 
  UNION 
  {?forename wdt:P31 wd:Q11879590} 
  UNION 
  {?forename wdt:P31 wd:Q12308941} 
  UNION 
  {?forename wdt:P31 wd:Q7452919} .
  VALUES ?forenameType {wd:Q7452919 wd:Q12308941 wd:Q11879590 wd:Q3409032}
}");
        $io->writeLn("");
        $io->writeln("<info>Sélection du fichier contenant  les genres : </info>");
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

        $countUpdates = 1;
        if ($handle) {
            $lineNumber = 0;
            while (($line = fgets($handle, 4096)) !== false) {
                if ($lineNumber > 0) {
                    $values = preg_split("/\t/", $line);
                    $Qname = chop(str_replace("http://www.wikidata.org/entity/", "", $values[0]));
                    $Qtype = chop(str_replace("http://www.wikidata.org/entity/", "", $values[1]));

                    $forenames = $this->forenameRepository->findByWikidata($Qname);
                    foreach ($forenames as $forename) {
                        $updated = $forename->setGenderFromQType($Qtype);
                        if ($updated) {
                            $this->em->persist($forename);
                            $countUpdates++;
                        }
                    }
                }

                if (($lineNumber % 500) == 0) {
                    $this->em->flush();
                    $this->em->clear();
                    $duration = microtime(true) - $start;
                    print "Import : $countUpdates (Line $lineNumber): $duration\n";
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
