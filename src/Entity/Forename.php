<?php

namespace App\Entity;

use App\Service\SlugGenerator;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table]
#[ORM\Index(name: 'idx_length', columns: ['labels_length'])]
#[ORM\Index(name: 'idx_labels', columns: ['labels'])]
#[ORM\Index(name: 'idx_label', columns: ['label'])]
#[ORM\Index(name: 'idx_wikidata', columns: ['wikidata'])]
#[ORM\Index(name: 'searchA', columns: ['A'])]
#[ORM\Index(name: 'searchB', columns: ['B'])]
#[ORM\Index(name: 'searchC', columns: ['C'])]
#[ORM\Index(name: 'searchD', columns: ['D'])]
#[ORM\Index(name: 'searchE', columns: ['E'])]
#[ORM\Index(name: 'searchF', columns: ['F'])]
#[ORM\Index(name: 'searchG', columns: ['G'])]
#[ORM\Index(name: 'searchH', columns: ['H'])]
#[ORM\Index(name: 'searchI', columns: ['I'])]
#[ORM\Index(name: 'searchJ', columns: ['J'])]
#[ORM\Index(name: 'searchK', columns: ['K'])]
#[ORM\Index(name: 'searchL', columns: ['L'])]
#[ORM\Index(name: 'searchM', columns: ['M'])]
#[ORM\Index(name: 'searchN', columns: ['N'])]
#[ORM\Index(name: 'searchO', columns: ['O'])]
#[ORM\Index(name: 'searchP', columns: ['P'])]
#[ORM\Index(name: 'searchQ', columns: ['Q'])]
#[ORM\Index(name: 'searchR', columns: ['R'])]
#[ORM\Index(name: 'searchS', columns: ['S'])]
#[ORM\Index(name: 'searchT', columns: ['T'])]
#[ORM\Index(name: 'searchU', columns: ['U'])]
#[ORM\Index(name: 'searchV', columns: ['V'])]
#[ORM\Index(name: 'searchW', columns: ['W'])]
#[ORM\Index(name: 'searchX', columns: ['X'])]
#[ORM\Index(name: 'searchY', columns: ['Y'])]
#[ORM\Index(name: 'searchZ', columns: ['Z'])]
#[ORM\Entity(repositoryClass: 'App\Repository\ForenameRepository')]
class Forename
{
    const GENDER_UNKNOWN = 0;
    const GENDER_FEMALE = 1;
    const GENDER_MALE = 2;
    const GENDER_BOTH = 3;
    const GENDER_OTHER = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $wikidata;

    #[ORM\Column(type: 'string', length: 255)]
    private $label;

    #[ORM\Column(type: 'string', length: 255)]
    private $labels;

    #[ORM\Column(type: 'integer')]
    private $labelsLength;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $A = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $B = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $C = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $D = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $E = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $F = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $G = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $H = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $I = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $J = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $K = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $L = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $M = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $N = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $O = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $P = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $Q = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $R = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $S = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $T = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $U = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $V = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $W = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $X = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $Y = 0;

    #[ORM\Column(type: 'smallint', nullable: true, options: ['default' => 0])]
    private $Z = 0;

    private $slugGenerator;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $gender;

    public function __construct(SlugGenerator $slugGenerator = null)
    {
        $this->slugGenerator = $slugGenerator;
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWikidata(): ?string
    {
        return $this->wikidata;
    }

    private function cleanWikidata(string $wikidata): string
    {
        return preg_replace("#http://www.wikidata.org/entity/#", "", $wikidata);
    }

    public function setWikidata(string $wikidata): self
    {
        $wikidata = $this->cleanWikidata($wikidata);
        $this->wikidata = $wikidata;

        return $this;
    }

    public function appendWikidata(string $wikidata): self
    {
        $wikidata = $this->cleanWikidata($wikidata);
        $this->setWikidata($this->getWikidata()."#".$wikidata);
        return $this;
    }


    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        chop($label);
        $this->label = $label;
        $this->labels = $this->slugGenerator->clean($label);
        $this->labelsLength = strlen($this->labels);
        $this->updateCharsCount();

        return $this;
    }

    public function getLabels(): ?string
    {
        return $this->labels;
    }

    public function setLabels(string $labels): self
    {
        $this->labels = $labels;
        $this->setLabelsLength(strlen($this->labels));
        $this->updateCharsCount();

        return $this;
    }

    private function updateCharsCount()
    {
        foreach (count_chars($this->labels, 1) as $i => $val) {
            $this->{chr($i)} = $val;
        }
    }

    public function getLabelsLength(): ?int
    {
        return $this->labelsLength;
    }

    public function setLabelsLength(int $labelsLength): self
    {
        $this->labelsLength = $labelsLength;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    private function translateGenderType(string $Q)
    {
        switch ($Q) {
            case "Q3409032":
                return Forename::GENDER_BOTH;
            case "Q11879590":
                return Forename::GENDER_FEMALE;
            case "Q12308941":
                return Forename::GENDER_MALE;
            case "Q7452919": // Serbian name
                return Forename::GENDER_OTHER;
            default:
                return Forename::GENDER_OTHER;
        }
        return Forename::GENDER_UNKNOWN;
    }

    public function setGenderFromQType(string $Q)
    {
        $convertedType = $this->translateGenderType($Q);
        $existingGenderType = $this->getGender();

        if ($convertedType == Forename::GENDER_OTHER) {
            if (is_null($this->getGender())) {
                $this->setGender(Forename::GENDER_UNKNOWN);
                return true;
            }
            return false;
        }

        if ($convertedType == $existingGenderType) {
            return false;
        }

        if ($existingGenderType == Forename::GENDER_UNKNOWN) {
            // On ne sait pas pour le moment, tout ce qu'on trouvera nosu intésse

            $this->setGender($convertedType);
            return true;
        } elseif (
            ($existingGenderType == Forename::GENDER_MALE) &&
            ($convertedType == Forename::GENDER_FEMALE)
        ) {
            // Getting FEMALE when MALE already set > both
            $this->setGender(Forename::GENDER_BOTH);
            return true;
        } elseif (
            ($existingGenderType == Forename::GENDER_FEMALE) &&
            ($convertedType == Forename::GENDER_MALE)
        ) {
            // Getting MALE when FEMALE already set > both
            $this->setGender(Forename::GENDER_BOTH);
            return true;
        } elseif (
            ($existingGenderType == Forename::GENDER_BOTH)
            &&
            ($convertedType == Forename::GENDER_MALE || $convertedType == Forename::GENDER_FEMALE)
        ) {
            // Getting MALE or FEMALE when BOTH already set, nothing to do
            return false;
        } else {
            print $Q . "\n";
            print $convertedType . "\n";
            print "DEALLL\n";
        }
    }
}
