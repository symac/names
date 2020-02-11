<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurnameRepository")
 *  @ORM\Table(indexes={
 *     @ORM\Index(name="idx_length", columns={"labels_length"}),
 *     @ORM\Index(name="idx_labels", columns={"labels"}),
 *     @ORM\Index(name="searchA", columns={"A"}),
 *     @ORM\Index(name="searchB", columns={"B"}),
 *     @ORM\Index(name="searchC", columns={"C"}),
 *     @ORM\Index(name="searchD", columns={"D"}),
 *     @ORM\Index(name="searchE", columns={"E"}),
 *     @ORM\Index(name="searchF", columns={"F"}),
 *     @ORM\Index(name="searchG", columns={"G"}),
 *     @ORM\Index(name="searchH", columns={"H"}),
 *     @ORM\Index(name="searchI", columns={"I"}),
 *     @ORM\Index(name="searchJ", columns={"J"}),
 *     @ORM\Index(name="searchK", columns={"K"}),
 *     @ORM\Index(name="searchL", columns={"L"}),
 *     @ORM\Index(name="searchM", columns={"M"}),
 *     @ORM\Index(name="searchN", columns={"N"}),
 *     @ORM\Index(name="searchO", columns={"O"}),
 *     @ORM\Index(name="searchP", columns={"P"}),
 *     @ORM\Index(name="searchQ", columns={"Q"}),
 *     @ORM\Index(name="searchR", columns={"R"}),
 *     @ORM\Index(name="searchS", columns={"S"}),
 *     @ORM\Index(name="searchT", columns={"T"}),
 *     @ORM\Index(name="searchU", columns={"U"}),
 *     @ORM\Index(name="searchV", columns={"V"}),
 *     @ORM\Index(name="searchW", columns={"W"}),
 *     @ORM\Index(name="searchX", columns={"X"}),
 *     @ORM\Index(name="searchY", columns={"Y"}),
 *     @ORM\Index(name="searchZ", columns={"Z"})
 * })
 */
class Surname
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $wikidata;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $labels;

    /**
     * @ORM\Column(type="integer")
     */
    private $labelsLength;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $A = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $B = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $C = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $D = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $E = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $F = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $G = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $H = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $I = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $J = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $K = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $L = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $M = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $N = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $O = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $P = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $Q = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $R = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $S = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $T = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $U = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $V = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $W = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $X = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $Y = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    private $Z = 0;

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

    public function setWikidata(string $wikidata): self
    {
        $this->wikidata = $wikidata;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabels(): ?string
    {
        return $this->labels;
    }

    public function setLabels(string $labels): self
    {
        $this->labels = $labels;

        return $this;
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
}
