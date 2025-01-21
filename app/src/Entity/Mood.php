<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MoodRepository::class)]
#[ApiResource]
class Mood
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_mood = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'has_mood')]
    private Collection $my_mood;

    public function __construct()
    {
        $this->my_mood = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getDateMood(): ?\DateTimeInterface
    {
        return $this->date_mood;
    }

    public function setDateMood(?\DateTimeInterface $date_mood): static
    {
        $this->date_mood = $date_mood;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMyMood(): Collection
    {
        return $this->my_mood;
    }

    public function addMyMood(User $myMood): static
    {
        if (!$this->my_mood->contains($myMood)) {
            $this->my_mood->add($myMood);
            $myMood->setHasMood($this);
        }

        return $this;
    }

    public function removeMyMood(User $myMood): static
    {
        if ($this->my_mood->removeElement($myMood)) {
            // set the owning side to null (unless already changed)
            if ($myMood->getHasMood() === $this) {
                $myMood->setHasMood(null);
            }
        }

        return $this;
    }
}
