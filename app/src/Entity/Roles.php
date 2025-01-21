<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
#[ApiResource]
class Roles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'has_role')]
    private Collection $role_owner;

    public function __construct()
    {
        $this->role_owner = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getRoleOwner(): Collection
    {
        return $this->role_owner;
    }

    public function addRoleOwner(User $roleOwner): static
    {
        if (!$this->role_owner->contains($roleOwner)) {
            $this->role_owner->add($roleOwner);
            $roleOwner->setHasRole($this);
        }

        return $this;
    }

    public function removeRoleOwner(User $roleOwner): static
    {
        if ($this->role_owner->removeElement($roleOwner)) {
            // set the owning side to null (unless already changed)
            if ($roleOwner->getHasRole() === $this) {
                $roleOwner->setHasRole(null);
            }
        }

        return $this;
    }
}
