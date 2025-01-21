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
    private ?bool $call_statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $call_date = null;

    #[ORM\OneToOne(inversedBy: 'callAlerts', cascade: ['persist', 'remove'])]
    private ?User $alert_between = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isCallStatut(): ?bool
    {
        return $this->call_statut;
    }

    public function setCallStatut(bool $call_statut): static
    {
        $this->call_statut = $call_statut;

        return $this;
    }

    public function getCallDate(): ?\DateTimeInterface
    {
        return $this->call_date;
    }

    public function setCallDate(\DateTimeInterface $call_date): static
    {
        $this->call_date = $call_date;

        return $this;
    }

    public function getAlertBetween(): ?User
    {
        return $this->alert_between;
    }

    public function setAlertBetween(?User $alert_between): static
    {
        $this->alert_between = $alert_between;

        return $this;
    }
}
