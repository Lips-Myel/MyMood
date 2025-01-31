<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CohortesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CohortesRepository::class)]
#[ApiResource]
class Cohortes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column]
    private ?bool $temporary = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'cohortes')]
    private Collection $cohortMember; // Correction de la casse ici
    private ?User $supervisor = null;

    public function __construct()
    {
        $this->cohortMember = new ArrayCollection(); // Correction de la casse ici
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isTemporary(): ?bool
    {
        return $this->temporary;
    }

    public function setTemporary(bool $temporary): static
    {
        $this->temporary = $temporary;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCohortMembers(): Collection
    {
        return $this->cohortMember; // Correction de la casse ici
    }

    public function addCohortMember(User $cohortMember): static
    {
        if (!$this->cohortMember->contains($cohortMember)) { // Correction de la casse ici
            $this->cohortMember->add($cohortMember); // Correction de la casse ici
        }

        return $this;
    }

    public function removeCohortMember(User $cohortMember): static
    {
        $this->cohortMember->removeElement($cohortMember); // Correction de la casse ici

        return $this;
    }

    public function getSupervisor(): ?User
    {
        return $this->supervisor;
    }

    public function setSupervisor(User $supervisor): static
    {
        $this->supervisor = $supervisor;
        return $this;
    }
}
