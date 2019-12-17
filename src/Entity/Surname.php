<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurnameRepository")
 * @ORM\Table(indexes={@ORM\Index(name="idx_length", columns={"labels_length"}),@ORM\Index(name="idx_labels", columns={"labels"})})
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
    private $q;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQ(): ?string
    {
        return $this->q;
    }

    public function setQ(string $q): self
    {
        $this->q = $q;

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
