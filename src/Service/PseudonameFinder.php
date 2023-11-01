<?php


namespace App\Service;


use App\Entity\Forename;
use App\Entity\Surname;
use Doctrine\ORM\EntityManagerInterface;

class PseudonameFinder
{
    private $em;
    private $slugGenerator;

    public function __construct(EntityManagerInterface $em, SlugGenerator $slugGenerator)
    {
        $this->em = $em;
        $this->slugGenerator = $slugGenerator;
    }

    public function constructLettersIndex(string $input)
    {
        $arrayLettersCount = [];
        for ($i = 0; $i < strlen($input); $i++) {
            $letter = substr($input, $i, 1);
            $letter = strtoupper($letter);

            if (isset($arrayLettersCount[$letter])) {
                // A partir de 1, c'est le 2 qui nous intéresse
                $arrayLettersCount[$letter] = 2;
            } else {
                $arrayLettersCount[$letter] = 1;
            }
        }

        // Create the letters index
        $lettersIndex = "";
        for ($i = 65; $i <= 90; $i++) {
            $letter = chr($i);
            if (!isset($arrayLettersCount[$letter])) {
                $lettersIndex .= $letter;
            }
        }
        return $lettersIndex;
    }

    private function buildQuery($slug, $surnameLength)
    {
        $tableSurname = $this->em->getClassMetadata(Forename::class)->getTableName();
        $tableForname = $this->em->getClassMetadata(Surname::class)->getTableName();

        $query = "select distinct A.label as surname, A.wikidata as qs, B.label as forename, B.wikidata as qf, A.gender as gender from $tableSurname as A, $tableForname as B WHERE ";

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

        $searchString = "NOT REGEXP '[" . join("", array_keys($lettersSlug)) . "]'";
        $query .= "(A.letters_index " . $searchString . ") AND (B.letters_index " . $searchString . ") AND \n";

        foreach ($lettersSlug as $letter => $count) {
            if ($count == 1) {
                $query .= "(A." . $letter . " = 1 xor B." . $letter . " = 1) AND \n";
            } else {
                $query .= "(A." . $letter . " >= 1 or B." . $letter . " >= 1) AND \n";
            }

        }

        $query .= "(A.labels_length = $surnameLength) AND (B.labels_length = " . (strlen($slug) - $surnameLength) . ")";
        return $query;

    }


    private function buildQueryv1($slug, $surnameLength)
    {
        $tableSurname = $this->em->getClassMetadata(Forename::class)->getTableName();
        $tableForname = $this->em->getClassMetadata(Surname::class)->getTableName();

        $query = "select distinct A.label as surname, A.wikidata as qs, B.label as forename, B.wikidata as qf, A.gender as gender from $tableSurname as A, $tableForname as B WHERE ";

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
            $query .= "(A." . $letter . " = 0 and B." . $letter . " = 0) AND ";
        }

        foreach ($lettersSlug as $letter => $count) {
            if ($count == 1) {
                $query .= "(A." . $letter . " = 1 xor B." . $letter . " = 1) AND ";
            } else {
                $query .= "(A." . $letter . " >= 1 or B." . $letter . " >= 1) AND ";
            }

        }

        $query .= "(A.labels_length = $surnameLength) AND (B.labels_length = " . (strlen($slug) - $surnameLength) . ")";
        return $query;
    }

    private function getResults($slug, $query)
    {
        $outputResults = [];
        $stmt = $this->em->getConnection()->prepare($query . " LIMIT 0,1000000");
        $stmt->execute();

        $results = $stmt->fetchAll(2);

        $matchingSlugs = 0;
        foreach ($results as $result) {
            $suggestion = [
                "g" => $result["gender"],
                "f" => $result["forename"],
                "s" => $result["surname"],
                "qf" => $result["qf"],
                "qs" => $result["qs"]
            ];
            $slugCombined = $this->slugGenerator->clean($suggestion["s"] . $suggestion["f"]);
            if ($slugCombined == $slug) {
                $outputResults[] = $suggestion;
                $matchingSlugs++;
            }
        }
        return $outputResults;
    }

    function search(string $slug, int $forenameLength)
    {
        $query = $this->buildQueryv1($slug, $forenameLength);
        $results = $this->getResults($slug, $query);
        return $results;
    }
}