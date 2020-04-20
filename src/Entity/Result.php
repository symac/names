<?php

namespace App\Entity;

use App\Service\SlugGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

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

    private $percentageDone;
    private $slugGenerator;


    /**
     * @ORM\Column(type="date", options={"default": "1970-01-01"})
     */
    private $createDate;

    /**
     * @ORM\Column(type="date", options={"default": "1970-01-01"})
     */
    private $viewDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $countAnagrams;

    public function __construct(SlugGenerator $slugGenerator)
    {
        $this->status = Result::STATUS_NEW;
        $this->slugGenerator = $slugGenerator;
        $this->resultSteps = new ArrayCollection();
        $this->setCreateDate(new \DateTime());
        $this->setViewDate(new \DateTime());
        $this->setCountAnagrams(0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    private function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function nextForenameLengthNeeded()
    {
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

    public function getFinished()
    {
        return $this->status === Result::STATUS_FINISH;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getSearchSlugified(): ?string
    {
        $str = str_replace([], ' ', $this->getSearch());
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", "-", $clean);
        return $clean;
    }

    public function setSearch(string $search): self
    {
        $this->search = $search;
        $this->setSlug($this->slugGenerator->clean($search));
        return $this;
    }

    /**
     * @return int
     */
    public function getPercentageDone(): int
    {
        if ($this->percentageDone) {
            return $this->percentageDone;
        }

        return intval( (($this->nextForenameLengthNeeded() - 2 )* 100) / (strlen($this->getSlug()) - 2) );
    }

    /**
     * @param mixed $percentageDone
     */
    public function setPercentageDone($percentageDone): void
    {
        $this->percentageDone = $percentageDone;
    }

    public function getSortedAnagrams(): array
    {
        $output = [];
        $steps = $this->getResultSteps();
        foreach ($steps as $step) {
            $output = array_merge($output, $step->getAnagrams());
        }

        $c = new \Collator('fr_FR');
        usort($output, function($a, $b) use ($c) { return $c->compare($a["s"]." ".$a["f"], $b["s"]." ".$b["f"]); });
        return $output;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getViewDate(): ?\DateTimeInterface
    {
        return $this->viewDate;
    }

    public function setViewDate(\DateTimeInterface $viewDate): self
    {
        $this->viewDate = $viewDate;

        return $this;
    }

    public function getCountAnagrams(): ?int
    {
        return $this->countAnagrams;
    }

    public function setCountAnagrams(int $countAnagrams): self
    {
        $this->countAnagrams = $countAnagrams;

        return $this;
    }

    public function countAnagrams()
    {
        $count = 0;
        foreach ($this->getResultSteps() as $resultStep) {
            $count += sizeof($resultStep->getAnagrams());
        }
        return $count;
    }

}
