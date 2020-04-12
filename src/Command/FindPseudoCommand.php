<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Surname;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FindPseudoCommand extends Command
{
    protected static $defaultName = 'app:find-pseudo';
    protected $slugGenerator;
    protected $em;

    public function __construct(string $name = null, SlugGenerator $slugGenerator, EntityManagerInterface $em)
    {
        $this->slugGenerator = $slugGenerator;
        $this->em = $em;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    private function buildQueryv4($slug, $surnameLength) {
        $tableSurname = $this->em->getClassMetadata(Forename::class)->getTableName();
        $tableForname = $this->em->getClassMetadata(Surname::class)->getTableName();

        $query = "select distinct A.label as surname, B.label as forename from $tableSurname as A, $tableForname as B WHERE ";

        $lettersSlug = [];
        $lettersSlugCode = count_chars($slug, 1);
        foreach ($lettersSlugCode as $code => $count) {
            $lettersSlug[chr($code)] = $count;
        }

        //$lettersSlug = array_unique($lettersSlug);
        $lettersNotSlug = [];
        foreach (range('A', 'Z') as $letter) {
            if (!isset($lettersSlug[$letter])) {
                array_push($lettersNotSlug, $letter);
            }
        }

        foreach ($lettersNotSlug as $letter) {
            $query .= "(A.".$letter." = 0 and B.".$letter." = 0) AND \n";
        }

        foreach ($lettersSlug as $letter => $count) {
            if ($count == 1) {
                $query .= "(A.labels like '%$letter%' xor B.labels like '%$letter%') AND \n";
            } else {
                $query .= "(A.labels like '%$letter%' or B.labels like '%$letter%') AND \n";
            }
            /* if ($count == 1) {
                $query .= "(A.".$letter." = 1 xor B.".$letter." = 1) AND \n";
            } else {
                $query .= "(A.".$letter." >= 1 or B.".$letter." >= 1) AND \n";
            } */

        }

        // $query = preg_replace("/ AND \n$/", "", $query);
        $query .= "(A.labels_length = $surnameLength) AND (B.labels_length = ".(strlen($slug) - $surnameLength).")";
        return $query;
    }

    private function buildQueryv3($slug, $surnameLength) {
        $tableSurname = $this->em->getClassMetadata(Forename::class)->getTableName();
        $tableForname = $this->em->getClassMetadata(Surname::class)->getTableName();

        $query = "select distinct A.label as surname, B.label as forename from $tableSurname as A, $tableForname as B WHERE ";

        $lettersSlug = [];
        $lettersSlugCode = count_chars($slug, 1);
        foreach ($lettersSlugCode as $code => $count) {
            $lettersSlug[chr($code)] = $count;
        }

        //$lettersSlug = array_unique($lettersSlug);
        $lettersNotSlug = [];
        foreach (range('A', 'Z') as $letter) {
            if (!isset($lettersSlug[$letter])) {
                array_push($lettersNotSlug, $letter);
            }
        }

        foreach ($lettersNotSlug as $letter) {
            $query .= "(A.labels not like '%$letter%' and B.labels not like '%$letter%') AND \n";
        }

        foreach ($lettersSlug as $letter => $count) {
            if ($count == 1) {
                $query .= "(A.labels like '%$letter%' xor B.labels like '%$letter%') AND \n";
            } else {
                $query .= "(A.labels like '%$letter%' or B.labels like '%$letter%') AND \n";
            }
        }

        // $query = preg_replace("/ AND \n$/", "", $query);
        $query .= "(A.labels_length = $surnameLength) AND (B.labels_length = ".(strlen($slug) - $surnameLength).")";
        return $query;
    }


    private function buildQueryv2($slug) {
        $tableSurname = $this->em->getClassMetadata(Forename::class)->getTableName();
        $tableForename = $this->em->getClassMetadata(Surname::class)->getTableName();

        $query = "select distinct $tableSurname.label as surname, $tableForename.label as forename from $tableSurname, $tableForename WHERE ";

        $querySurname = "select count(*) from $tableSurname WHERE ";
        $queryForename = "select count(*) from $tableForename WHERE ";
        $lettersSlug = [];
        foreach (range('A', 'Z') as $letter) {
            $lettersSlug[$letter] = 0;
        }

        foreach (count_chars($slug, 1) as $i => $val) {
            $lettersSlug[chr($i)] = $val;
        }

        foreach ($lettersSlug as $letter => $count) {
            if ($count == 0 ) {
                $query .= "($tableForename.".$letter." = 0) AND \n($tableSurname.".$letter." = 0) AND \n";
                $querySurname .= "$tableSurname.$letter = 0 AND ";
                $queryForename .= "$tableForename.$letter = 0 AND ";
            } elseif ($count == 1) {
                $query .= "(($tableForename.".$letter." = 1) XOR ($tableSurname.".$letter." = 1)) AND \n";
                $querySurname .= "$tableSurname.$letter <= 1 AND ";
                $queryForename .= "$tableForename.$letter <= 1 AND ";
            }
            else {
                $query .= "($tableForename.".$letter." + $tableSurname.".$letter." = $count) AND \n";
                $querySurname .= "$tableSurname.$letter <= $count AND ";
                $queryForename .= "$tableForename.$letter <= $count AND ";
            }
        }

        // $query .= "($tableForename.labels_length + $tableSurname.labels_length) = ".strlen($slug);
        $surnameLength = 10;
        $query .= "(A.labels_length = $surnameLength) AND (B.labels_length = ".(strlen($slug) - $surnameLength).")";
        return $query;
    }


    private function buildQueryv1($slug) {
        $tableSurname = $this->em->getClassMetadata(Forename::class)->getTableName();
        $tableForname = $this->em->getClassMetadata(Surname::class)->getTableName();

        $query = "select distinct A.label as surname, B.label as forename from $tableSurname as A, $tableForname as B WHERE ";
        $lettersSlug = str_split($slug);
        $lettersSlug = array_unique($lettersSlug);
        $lettersNotSlug = [];
        foreach (range('A', 'Z') as $letter) {
            if (!in_array($letter, $lettersSlug)) {
                array_push($lettersNotSlug, $letter);
            }
        }

        foreach ($lettersNotSlug as $letter) {
            $query .= "(A.labels not like '%$letter%' and B.labels not like '%$letter%') AND \n";
        }

        foreach ($lettersSlug as $letter) {
            $query .= "(A.labels like '%$letter%' or B.labels like '%$letter%') AND \n";
        }

        // $query = preg_replace("/ AND \n$/", "", $query);
        $query .= "(A.labels_length + B.labels_length) = ".strlen($slug);
        return $query;
    }

    private function getResults($slug, $query) {
        $start = microtime(true);
        $stmt = $this->em->getConnection()->prepare($query." LIMIT 0,1000000");
        $stmt->execute();

        $results = $stmt->fetchAll(2);
        $resultsNumber = sizeof($results);
        $matchingSlugs = 0;
        foreach ($results as $result) {
            $suggestion = $result["surname"]." ".$result["forename"];
            $slugCombined = $this->slugGenerator->clean($suggestion);

            if ($slugCombined == $slug) {
                // dd($result);
                print "### ".$suggestion." ###\n";
                $matchingSlugs++;
            }
        }

        print "Time taken : ".(microtime(true) - $start)."\n";
        print "$resultsNumber rows\n";
        print "$matchingSlugs match\n";
        if ($resultsNumber > 0) {
            print "Ratio : ".sprintf("%f", ($matchingSlugs * 100 / $resultsNumber))."\n";

        }

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $name = "Sylvain Dupont";
        $slug = $this->slugGenerator->clean($name);
        // $query = $this->buildQueryv1($slug);
        //$query = $this->buildQueryv2($slug);
        for ($i = 4; $i <= strlen($slug) - 3; $i++) {
            print "#### $i CALL #####\n";
            $query = $this->buildQueryv4($slug, $i);
            $this->getResults($slug, $query);
        }

        print "Total time taken : ".(microtime(true) - $start)."\n";
        return 1;
    }
}
