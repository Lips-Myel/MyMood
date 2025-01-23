<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\HistoricalMoodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoricalMoodRepository::class)]
#[ApiResource]
class HistoricalMood
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Mood::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mood $mood = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $score = null;

    public function __construct(User $user, Mood $mood)
    {
        $this->user = $user;
        $this->mood = $mood;
        $this->date = new \DateTime();
        $this->score = $mood->getScore();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getMood(): ?Mood
    {
        return $this->mood;
    }

    public function setMood(Mood $mood): self
    {
        $this->mood = $mood;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }
}