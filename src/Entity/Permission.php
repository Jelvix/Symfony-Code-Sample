<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\PermissionRepository")
 * @ORM\Table(name="permissions")
 *
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("full")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"full", "short"})
     * @Assert\NotBlank
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full", "short"})
     * @Assert\NotBlank
     */
    private $resource;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"full", "short"})
     */
    private $is_granted;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"full", "short"})
     * @Assert\NotBlank
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"full", "short"})
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"full", "short"})
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function setResource(string $resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    public function getIsGranted(): ?bool
    {
        return $this->is_granted;
    }

    public function setIsGranted(bool $is_granted): self
    {
        $this->is_granted = $is_granted;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

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
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

}
