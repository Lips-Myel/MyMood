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
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column]
    private ?bool $temporary = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'cohortes')]
    private Collection $Cohortes_Member;

    public function __construct()
    {
        $this->Cohortes_Member = new ArrayCollection();
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
        return $this->start_date;
    }

    public function setStartDate(?\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

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
    public function getCohortesMember(): Collection
    {
        return $this->Cohortes_Member;
    }

    public function addCohortesMember(User $cohortesMember): static
    {
        if (!$this->Cohortes_Member->contains($cohortesMember)) {
            $this->Cohortes_Member->add($cohortesMember);
        }

        return $this;
    }

    public function removeCohortesMember(User $cohortesMember): static
    {
        $this->Cohortes_Member->removeElement($cohortesMember);

        return $this;
    }
}
