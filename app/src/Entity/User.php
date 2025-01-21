<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_connection = null;

    #[ORM\Column(nullable: true)]
    private ?bool $black_list = null;

    #[ORM\ManyToOne(inversedBy: 'my_mood')]
    private ?Mood $has_mood = null;

    // Constructeur pour injecter PasswordHasherInterface
    private PasswordHasherInterface $passwordHasher;

    #[ORM\OneToOne(mappedBy: 'alert_between', cascade: ['persist', 'remove'])]
    private ?CallAlerts $callAlerts = null;

    #[ORM\ManyToOne(inversedBy: 'role_owner')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Roles $has_role = null;

    /**
     * @var Collection<int, Cohortes>
     */
    #[ORM\ManyToMany(targetEntity: Cohortes::class, mappedBy: 'Cohortes_Member')]
    private Collection $cohortes;

    public function __construct(PasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->cohortes = new ArrayCollection();
    }

    // Implémentation de UserInterface
    public function getUserIdentifier(): string
    {
        return $this->email;  // Utilisation de l'email comme identifiant
    }

    public function getUsername(): string
    {
        return $this->email;  // Utilisation de l'email comme identifiant
    }

    public function getRoles(): array
    {
        // Retourne un tableau de rôles, en s'assurant que l'utilisateur a un rôle de base
        $roles = [$this->role ?: 'ROLE_USER'];  // Par défaut ROLE_USER
        return array_unique($roles); // Enlever les doublons potentiels
    }

    public function setRoles(array $roles): static
    {
        // Si vous avez besoin de gérer plusieurs rôles
        $this->role = implode(',', $roles);  // Exemple : 'ROLE_ADMIN,ROLE_USER'
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        // Hachez le mot de passe avant de le stocker
        $this->password = $this->passwordHasher->hash($password);
        return $this;
    }

    public function getSalt(): ?string
    {
        // Renvoie null car Symfony n'a pas besoin de salt pour bcrypt ou argon2i
        return null;
    }

    public function eraseCredentials(): void
    {
        // Efface les informations sensibles si nécessaire
    }

    // Getters et setters pour les autres propriétés
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->last_connection;
    }

    public function setLastConnection(?\DateTimeInterface $last_connection): static
    {
        $this->last_connection = $last_connection;
        return $this;
    }

    public function isBlackList(): ?bool
    {
        return $this->black_list;
    }

    public function setBlackList(?bool $black_list): static
    {
        $this->black_list = $black_list;
        return $this;
    }

    public function getHasMood(): ?Mood
    {
        return $this->has_mood;
    }

    public function setHasMood(?Mood $has_mood): static
    {
        $this->has_mood = $has_mood;
        return $this;
    }

    public function getCallAlerts(): ?CallAlerts
    {
        return $this->callAlerts;
    }

    public function setCallAlerts(?CallAlerts $callAlerts): static
    {
        // unset the owning side of the relation if necessary
        if ($callAlerts === null && $this->callAlerts !== null) {
            $this->callAlerts->setAlertBetween(null);
        }

        // set the owning side of the relation if necessary
        if ($callAlerts !== null && $callAlerts->getAlertBetween() !== $this) {
            $callAlerts->setAlertBetween($this);
        }

        $this->callAlerts = $callAlerts;

        return $this;
    }

    public function getHasRole(): ?Roles
    {
        return $this->has_role;
    }

    public function setHasRole(?Roles $has_role): static
    {
        $this->has_role = $has_role;

        return $this;
    }

    /**
     * @return Collection<int, Cohortes>
     */
    public function getCohortes(): Collection
    {
        return $this->cohortes;
    }

    public function addCohorte(Cohortes $cohorte): static
    {
        if (!$this->cohortes->contains($cohorte)) {
            $this->cohortes->add($cohorte);
            $cohorte->addCohortesMember($this);
        }

        return $this;
    }

    public function removeCohorte(Cohortes $cohorte): static
    {
        if ($this->cohortes->removeElement($cohorte)) {
            $cohorte->removeCohortesMember($this);
        }

        return $this;
    }
}
