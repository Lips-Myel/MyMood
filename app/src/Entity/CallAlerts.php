<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CallAlertsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CallAlertsRepository::class)]
#[ApiResource]
class CallAlerts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $callStatut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $callDate = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'callAlerts')]
    #[ORM\JoinColumn(nullable: false)] // L'utilisateur est obligatoire pour un CallAlert
    private ?User $alertBetween = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isCallStatut(): ?bool
    {
        return $this->callStatut;
    }

    public function setCallStatut(bool $callStatut): static
    {
        $this->callStatut = $callStatut;

        return $this;
    }

    public function getCallDate(): ?\DateTimeInterface
    {
        return $this->callDate;
    }

    public function setCallDate(\DateTimeInterface $callDate): static
    {
        $this->callDate = $callDate;

        return $this;
    }

    public function getAlertBetween(): ?User
    {
        return $this->alertBetween;
    }

    public function setAlertBetween(?User $alertBetween): static
    {
        $this->alertBetween = $alertBetween;

        return $this;
    }
}
