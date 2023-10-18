<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\QuizzCategoryRepository')]
class QuizzCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(targetEntity: 'App\Entity\Quizz', mappedBy: 'quizzCategory')]
    private $Quizzes;

    #[ORM\Column(type: 'string', length: 255)]
    private $wikidataOccupation;

    public function __construct()
    {
        $this->Quizzes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Quizz[]
     */
    public function getQuizzes(): Collection
    {
        return $this->Quizzes;
    }

    public function addQuiz(Quizz $quiz): self
    {
        if (!$this->Quizzes->contains($quiz)) {
            $this->Quizzes[] = $quiz;
            $quiz->setQuizzCategory($this);
        }

        return $this;
    }

    public function removeQuiz(Quizz $quiz): self
    {
        if ($this->Quizzes->contains($quiz)) {
            $this->Quizzes->removeElement($quiz);
            // set the owning side to null (unless already changed)
            if ($quiz->getQuizzCategory() === $this) {
                $quiz->setQuizzCategory(null);
            }
        }

        return $this;
    }

    public function getWikidataOccupation(): ?string
    {
        return $this->wikidataOccupation;
    }

    public function setWikidataOccupation(string $wikidataOccupation): self
    {
        $this->wikidataOccupation = $wikidataOccupation;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
