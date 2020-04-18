<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuizzRepository")
 */
class Quizz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wikidata;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $answer;

    /**
     * @ORM\Column(type="text")
     */
    private $question;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $secret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $anagram;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Result")
     */
    private $Result;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
        $this->secret = bin2hex(random_bytes(5));
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

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getAnagram(): ?string
    {
        return $this->anagram;
    }

    public function setAnagram(?string $anagram): self
    {
        $this->anagram = $anagram;

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
}
