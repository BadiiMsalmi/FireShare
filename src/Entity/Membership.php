<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Membership
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $joinDate = null;



    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: "memberships")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $group = null;


    #[ORM\ManyToOne(inversedBy: 'Memberships')]
    private ?User $membershipUser = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJoinDate(): ?\DateTimeInterface
    {
        return $this->joinDate;
    }

    public function setJoinDate(\DateTimeInterface $joinDate): self
    {
        $this->joinDate = $joinDate;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getMembershipUser(): ?User
    {
        return $this->membershipUser;
    }

    public function setMembershipUser(?User $membershipUser): static
    {
        $this->membershipUser = $membershipUser;

        return $this;
    }
}
