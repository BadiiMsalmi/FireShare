<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "`group`")]
#[ORM\Entity]
class Groups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "datetime")]
    private ?DateTimeInterface $createdAt = null;


    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: "group")]
    private Collection $memberships;


    #[ORM\ManyToOne(inversedBy: 'groupsManaging')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Moderator = null;



    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'group', orphanRemoval: true)]
    private Collection $postes;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->postes = new ArrayCollection();
        $this->createdAt = new \DateTime('now');

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setGroup($this);
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getGroup() === $this) {
                $post->setGroup(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Membership[]
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Membership $membership): self
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships[] = $membership;
            $membership->setGroup($this);
        }
        return $this;
    }

    public function removeMembership(Membership $membership): self
    {
        if ($this->memberships->removeElement($membership)) {
            if ($membership->getGroup() === $this) {
                $membership->setGroup(null);
            }
        }
        return $this;
    }

    public function getModerator(): ?User
    {
        return $this->Moderator;
    }

    public function setModerator(?User $Moderator): static
    {
        $this->Moderator = $Moderator;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPostes(): Collection
    {
        return $this->postes;
    }

    public function addPoste(Post $poste): static
    {
        if (!$this->postes->contains($poste)) {
            $this->postes->add($poste);
            $poste->setGroupOfPost($this);
        }

        return $this;
    }

    public function removePoste(Post $poste): static
    {
        if ($this->postes->removeElement($poste)) {
            // set the owning side to null (unless already changed)
            if ($poste->getGroupOfPost() === $this) {
                $poste->setGroupOfPost(null);
            }
        }

        return $this;
    }
}
