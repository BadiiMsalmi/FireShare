<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('UPVOTE', 'DOWNVOTE')")]
    private ?string $voteType = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;


    #[ORM\ManyToOne(inversedBy: 'VOTES')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVoteType(): ?string
    {
        return $this->voteType;
    }

    public function setVoteType(string $voteType): self
    {
        $this->voteType = $voteType;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;
        return $this;
    }

    public function getUserVote(): ?User
    {
        return $this->userVote;
    }

    public function setUserVote(?User $userVote): static
    {
        $this->userVote = $userVote;

        return $this;
    }

    public function getVoteUser(): ?User
    {
        return $this->voteUser;
    }

    public function setVoteUser(?User $voteUser): static
    {
        $this->voteUser = $voteUser;

        return $this;
    }
}
