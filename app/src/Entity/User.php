<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiSubresource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;
use App\Controller\UserCreateController;

/**
 * Classe représentant un utilisateur.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/create-users',
            controller: UserCreateController::class,
            name: 'app_create_users',
            extraProperties: [
                'openapi' => [
                    'summary' => 'Créer un utilisateur personnalisé',
                    'description' => 'Une route POST personnalisée pour créer un utilisateur',
                ]
            ]
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeInterface $lastConnection = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['user:read', 'user:write'])]
    private ?bool $isBlackListed = false;

    #[ORM\ManyToOne(targetEntity: Mood::class, inversedBy: 'myMoods')]
    #[ORM\JoinColumn(nullable: true)] // Relation nullable
    #[ApiSubresource]
    #[Groups(['mood:read'])]
    private ?Mood $hasMood = null;

    #[ORM\OneToMany(mappedBy: 'alertBetween', targetEntity: CallAlerts::class, cascade: ['persist', 'remove'])]
    #[ApiSubresource]
    private Collection $callAlerts;

    #[ORM\ManyToOne(targetEntity: Roles::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiSubresource]
    #[Groups(['role:read'])]
    private ?Roles $hasRole = null;

    /**
     * @var Collection<int, Cohortes>
     */
    #[ORM\ManyToMany(targetEntity: Cohortes::class, mappedBy: 'cohortMember')]
    #[ApiSubresource]
    #[Groups(['cohortes:read', 'cohortes:write'])]
    private Collection $cohortes;
    

    public function __construct()
    {
        $this->cohortes = new ArrayCollection();
        $this->callAlerts = new ArrayCollection(); // S'assure qu'aucune erreur ne survienne
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->hasRole ? [$this->hasRole->getName()] : ['ROLE_USER'];
    }

    public function eraseCredentials(): void {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->lastConnection;
    }

    public function setLastConnection(?\DateTimeInterface $lastConnection): static
    {
        $this->lastConnection = $lastConnection;
        return $this;
    }

    public function isBlackList(): ?bool
    {
        return $this->isBlackListed;
    }

    public function setBlackList(?bool $isBlackListed): static
    {
        $this->isBlackListed = $isBlackListed;
        return $this;
    }

    public function getHasMood(): ?Mood
    {
        return $this->hasMood;
    }

    public function setHasMood(?Mood $hasMood): static
    {
        $this->hasMood = $hasMood;
        return $this;
    }

    /**
     * @return Collection<int, CallAlerts>
     */
    public function getCallAlerts(): Collection
    {
        return $this->callAlerts;
    }

    public function addCallAlert(CallAlerts $callAlert): static
    {
        if (!$this->callAlerts->contains($callAlert)) {
            $this->callAlerts->add($callAlert);
            $callAlert->setAlertBetween($this); // Associe l'alerte à l'utilisateur
        }

        return $this;
    }

    public function removeCallAlert(CallAlerts $callAlert): static
    {
        if ($this->callAlerts->removeElement($callAlert)) {
            if ($callAlert->getAlertBetween() === $this) {
                $callAlert->setAlertBetween(null); // Dissocie l'alerte de l'utilisateur
            }
        }

        return $this;
    }

    public function getHasRole(): ?Roles
    {
        return $this->hasRole;
    }

    public function setHasRole(?Roles $hasRole): static
    {
        $this->hasRole = $hasRole;
        return $this;
    }

    /**
     * @return Collection<int, Cohortes>
     */
    public function getCohortes(): Collection
    {
        return $this->cohortes;
    }

    public function addCohort(Cohortes $cohorte): static
    {
        if (!$this->cohortes->contains($cohorte)) {
            $this->cohortes->add($cohorte);
            $cohorte->addCohortMember($this);
        }

        return $this;
    }

    public function removeCohort(Cohortes $cohorte): static
    {
        if ($this->cohortes->removeElement($cohorte)) {
            $cohorte->removeCohortMember($this);
        }

        return $this;
    }
}
