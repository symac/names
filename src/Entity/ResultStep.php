<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\ResultStepRepository')]
class ResultStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $forenameLength;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Result', inversedBy: 'resultSteps')]
    #[ORM\JoinColumn(nullable: false)]
    private $Result;

    #[ORM\Column(type: 'array', nullable: true)]
    private $anagrams = [];

    #[ORM\Column(type: 'float', nullable: true)]
    private $duration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForenameLength(): ?int
    {
        return $this->forenameLength;
    }

    public function setForenameLength(int $forenameLength): self
    {
        $this->forenameLength = $forenameLength;

        return $this;
    }

    public function getResult(): ?Result
    {
        return $this->Result;
    }

    public function setResult(?Result $Result): self
    {
        $this->Result = $Result;

        return $this;
    }

    public function getAnagrams(): ?array
    {
        return $this->anagrams;
    }

    public function setAnagrams(?array $anagrams): self
    {
        $this->anagrams = $anagrams;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(?float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
