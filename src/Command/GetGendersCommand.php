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

    public function __construct(string $name = null, EntityManagerInterface $em, ForenameRepository $forenameRepository)
    {
        $this->forenameRepository = $forenameRepository;
        $this->em = $em;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Load genders from TSV File')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Chargement d'un fichier des prénoms");
        $io->writeln("Les fichiers correspondent au résultat SparQL des deux requêtes suivantes : ");
        $io->title("Requête :");
        $io->writeln("SELECT distinct ?forename ?forenameType
WHERE
{
  ?forename wdt:P31/wdt:P279*  wd:Q202444 .
  ?forename wdt:P31 ?forenameType .
  MINUS {
    ?forename wdt:P31 wd:Q202444
  }
}");
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

        $countUpdates = 1;
        if ($handle) {
            $lineNumber = 0;
            while (($line = fgets($handle, 4096)) !== false) {
                if ($lineNumber > 0) {
                    $values = preg_split("/\t/", $line);
                    $Qname = chop(str_replace("http://www.wikidata.org/entity/", "" ,$values[0]));
                    $Qtype = chop(str_replace("http://www.wikidata.org/entity/", "" ,$values[1]));

                    $forenames = $this->forenameRepository->findBy(["wikidata" => $Qname]);
                    foreach ($forenames as $forename) {
                        $updated = $forename->setGenderFromQType($Qtype);
                        if ($updated) {
                            $this->em->persist($forename);
                            $countUpdates++;
                        }
                    }
                }

                if ( ($countUpdates % 5000) == 0) {
                    print $countUpdates."\n";
                    $duration = microtime(true) - $start;
                    print "Import : $countUpdates - start flush [$duration]\n";
                    $start = microtime(true);
                    $this->em->flush();
                    $duration = microtime(true) - $start;
                    print "Import : $countUpdates - end flush [$duration]\n";
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
