<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultRepository")
 */
class Result
{
    const STATUS_NEW = 0;
    const STATUS_RUNNING = 1;
    const STATUS_FINISH = 2;
    const STATUS_ERROR = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ResultStep", mappedBy="Result")
     */
    private $resultSteps;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $search;

    public function __construct()
    {
        $this->status = Result::STATUS_NEW;
        $this->resultSteps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function nextForenameLengthNeeded() {
        $maxLength = 3;
        foreach ($this->getResultSteps() as $resultStep) {
            if ($resultStep->getForenameLength() > $maxLength) {
                $maxLength = $resultStep->getForenameLength();
            }
        }

        if ($maxLength > (strlen($this->getSlug()) - 3)) {
            return null;
        }
        return ($maxLength + 1);
    }

    /**
     * @return Collection|ResultStep[]
     */
    public function getResultSteps(): Collection
    {
        return $this->resultSteps;
    }

    public function addResultStep(ResultStep $resultStep): self
    {
        if (!$this->resultSteps->contains($resultStep)) {
            $this->resultSteps[] = $resultStep;
            $resultStep->setResult($this);
        }

        return $this;
    }

    public function removeResultStep(ResultStep $resultStep): self
    {
        if ($this->resultSteps->contains($resultStep)) {
            $this->resultSteps->removeElement($resultStep);
            // set the owning side to null (unless already changed)
            if ($resultStep->getResult() === $this) {
                $resultStep->setResult(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFinished() {
        return $this->status === Result::STATUS_FINISH;
    }

    public function countAnagrams() {
        $count = 0;
        foreach ($this->getResultSteps() as $resultStep) {
            $count += sizeof($resultStep->getAnagrams());
        }
        return $count;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(string $search): self
    {
        $this->search = $search;

        return $this;
    }
}
