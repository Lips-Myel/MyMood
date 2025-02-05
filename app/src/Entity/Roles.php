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
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'hasRole')]
    private Collection $roleOwners;

    public function __construct()
    {
        $this->roleOwners = new ArrayCollection();
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
    public function getRoleOwners(): Collection
    {
        return $this->roleOwners;
    }

    public function addRoleOwner(User $roleOwner): static
    {
        if (!$this->roleOwners->contains($roleOwner)) {
            $this->roleOwners->add($roleOwner);
            $roleOwner->setHasRole($this);
        }

        return $this;
    }

    public function removeRoleOwner(User $roleOwner): static
    {
        if ($this->roleOwners->removeElement($roleOwner)) {
            // set the owning side to null (unless already changed)
            if ($roleOwner->getHasRole() === $this) {
                $roleOwner->setHasRole(null);
            }
        }

        return $this;
    }
}
