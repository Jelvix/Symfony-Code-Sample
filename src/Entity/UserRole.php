<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRoleRepository")
 * @ORM\Table(name="user_roles")
 */
class UserRole
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("full")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full", "short"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("full")
     * @Type(name = "DateTime<'Y-m-d H:i:s'>")
     */
    private $created_at;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UserRole")
     * @Groups({"full", "short"})
     */
    private $parentRoles;


    public function __construct()
    {
        $this->parentRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getParentRoles(): Collection
    {
        return $this->parentRoles;
    }

    public function addParentRole(self $parentRole): self
    {
        if (!$this->parentRoles->contains($parentRole)) {
            $this->parentRoles[] = $parentRole;
        }

        return $this;
    }

    public function removeParentRole(self $parentRole): self
    {
        if ($this->parentRoles->contains($parentRole)) {
            $this->parentRoles->removeElement($parentRole);
        }

        return $this;
    }

    public function clearParentRoles(): self
    {
        $this->parentRoles = new ArrayCollection();
        return $this;
    }

}
