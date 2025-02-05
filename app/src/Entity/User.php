<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Repository\UserRepository;
use App\Controller\UserCreateController;
use App\Controller\UserUpdateController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/admin/create-users',
            controller: UserCreateController::class,
            name: 'app_create_users',
            extraProperties: [
                'openapi' => [
                    'summary' => 'Créer un utilisateur personnalisé',
                    'description' => 'Une route POST personnalisée pour créer un utilisateur',
                ]
            ]
        ),
        new GetCollection(
            uriTemplate: '/users',
            name: 'app_get_users',
            extraProperties: [
                'openapi' => [
                    'summary' => 'Récupérer tous les utilisateurs',
                    'description' => 'Retourne la liste complète des utilisateurs',
                ]
            ]
        ),
        new Get(
            uriTemplate: '/users/{id}',
            name: 'app_get_user_by_id',
            extraProperties: [
                'openapi' => [
                    'summary' => 'Récupérer un utilisateur par ID',
                    'description' => 'Retourne un utilisateur spécifique basé sur son ID',
                ]
            ]
        ),
        new Patch(
            uriTemplate: '/users/{id}',
            controller: UserUpdateController::class . '::updateUser',
            name: 'app_update_user',
            extraProperties: [
                'openapi' => [
                    'summary' => 'Mettre à jour un utilisateur',
                    'description' => 'Modifie les informations d’un utilisateur, y compris son mot de passe',
                ]
            ]
        ),
        new Delete(
            uriTemplate: '/users/{id}',
            name: 'app_delete_user',
            extraProperties: [
                'openapi' => [
                    'summary' => 'Supprimer un utilisateur',
                    'description' => 'Supprime un utilisateur de la base de données',
                ]
            ]
        ),
        new Post(
            uriTemplate: '/users/{id}/reset-password',
            controller: UserUpdateController::class . '::resetPassword',
            name: 'app_reset_password',
            extraProperties: [
                'openapi' => [
                    'summary' => 'Réinitialiser le mot de passe',
                    'description' => 'Génère un nouveau mot de passe pour un utilisateur et le met à jour',
                ]
            ]
        )
    ]
)]

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
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
    #[Groups(['mood:read'])]
    private ?Mood $hasMood = null;

    #[ORM\OneToMany(mappedBy: 'alertBetween', targetEntity: CallAlerts::class, cascade: ['persist', 'remove'])]
    private Collection $callAlerts;

    #[ORM\ManyToOne(targetEntity: Roles::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['role:read'])]
    private ?Roles $hasRole = null;

    /**
     * @var Collection<int, Cohortes>
     */
    #[ORM\ManyToMany(targetEntity: Cohortes::class, mappedBy: 'cohortMember')]
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
    


    // Ajoute cette méthode pour enrichir le token avec tes données personnalisées
        public function getJWTCustomClaims(): array
        {
            return [
                'id' => $this->getId(),
                'email' => $this->getEmail(),
                'first_name' => $this->getFirstName(),
                'last_name' => $this->getLastName(),
                'roles' => $this->getRoles(),
            ];
        }

        // Implémentation de la méthode obligatoire (peut rester vide si inutile)
        public static function createFromPayload($username, array $payload)
        {
            // Ici, tu peux récupérer les données du payload pour recréer un utilisateur si nécessaire
            return new self();
        }
}


