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

    private function buildQuery($slug, $surnameLength)
    {
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
            $query .= "(A." . $letter . " = 0 and B." . $letter . " = 0) AND \n";
        }

        foreach ($lettersSlug as $letter => $count) {
            if ($count == 1) {
                $query .= "(A.".$letter." = 1 xor B.".$letter." = 1) AND \n";
            } else {
                $query .= "(A.".$letter." >= 1 or B.".$letter." >= 1) AND \n";
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
        $resultsNumber = sizeof($results);
        $matchingSlugs = 0;
        foreach ($results as $result) {
            $suggestion = $result["surname"] . " " . $result["forename"];
            $slugCombined = $this->slugGenerator->clean($suggestion);

            if ($slugCombined == $slug) {
                $outputResults[] = $suggestion;
                $matchingSlugs++;
            }
        }
        return $outputResults;
    }

    function search(string $slug, int $forenameLength)
    {
        $query = $this->buildQuery($slug, $forenameLength);
        $results = $this->getResults($slug, $query);
        return $results;
    }
}