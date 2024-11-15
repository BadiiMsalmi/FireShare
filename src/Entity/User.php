<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: "string")]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(type: "integer")]
    private int $karmaPoints;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: "author")]
    private Collection $posts;

    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: 'membershipUser')]
    private Collection $Memberships;

    #[ORM\OneToMany(targetEntity: Groups::class, mappedBy: 'Moderator', orphanRemoval: true)]
    private Collection $groupsManaging;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'userCommented')]
    private Collection $Comments;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'userNotification', orphanRemoval: true)]
    private Collection $Notifications;

    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $votes;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->Memberships = new ArrayCollection();
        $this->groupsManaging = new ArrayCollection();
        $this->Comments = new ArrayCollection();
        $this->Notifications = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getKarmaPoints(): int
    {
        return $this->karmaPoints;
    }

    public function setKarmaPoints(int $karmaPoints): static
    {
        $this->karmaPoints = $karmaPoints;

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post) && $post->getAuthor() === $this) {
            $post->setAuthor(null);
        }

        return $this;
    }

    public function getMemberships(): Collection
    {
        return $this->Memberships;
    }

    public function addMembership(Membership $membership): static
    {
        if (!$this->Memberships->contains($membership)) {
            $this->Memberships[] = $membership;
            $membership->setMembershipUser($this);
        }

        return $this;
    }

    public function removeMembership(Membership $membership): static
    {
        if ($this->Memberships->removeElement($membership) && $membership->getMembershipUser() === $this) {
            $membership->setMembershipUser(null);
        }

        return $this;
    }

    public function getGroupsManaging(): Collection
    {
        return $this->groupsManaging;
    }

    public function addGroupManaging(Groups $group): static
    {
        if (!$this->groupsManaging->contains($group)) {
            $this->groupsManaging[] = $group;
            $group->setModerator($this);
        }

        return $this;
    }

    public function removeGroupManaging(Groups $group): static
    {
        if ($this->groupsManaging->removeElement($group) && $group->getModerator() === $this) {
            $group->setModerator(null);
        }

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->Comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->Comments->contains($comment)) {
            $this->Comments[] = $comment;
            $comment->setUserCommented($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->Comments->removeElement($comment) && $comment->getUserCommented() === $this) {
            $comment->setUserCommented(null);
        }

        return $this;
    }

    public function getNotifications(): Collection
    {
        return $this->Notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->Notifications->contains($notification)) {
            $this->Notifications[] = $notification;
            $notification->setUserNotification($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->Notifications->removeElement($notification) && $notification->getUserNotification() === $this) {
            $notification->setUserNotification(null);
        }

        return $this;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setVoteUser($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote) && $vote->getVoteUser() === $this) {
            $vote->setVoteUser(null);
        }

        return $this;
    }
}
