<?php
// src/Entity/User.php - VERSION COMPLÈTE AVEC TOUTES LES RELATIONS

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: "string")]
    private string $password;

    #[ORM\Column(type: "string", length: 50)]
    private string $role;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(nullable: true)]
    private ?string $cin = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isValidated = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    // ============================================
    // RELATIONS FAVORITES
    // ============================================
    #[ORM\ManyToMany(targetEntity: Business::class)]
    #[ORM\JoinTable(name: "favorite_business")]
    private Collection $favorites;

    // ============================================
    // RELATIONS SUBSCRIPTIONS
    // ============================================

    // Abonnements de l'utilisateur (en tant que subscriber)
    #[ORM\OneToMany(mappedBy: 'subscriber', targetEntity: Subscription::class, cascade: ['remove'])]
    private Collection $subscriptions;

    // Abonnés de l'utilisateur (en tant que provider)
    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Subscription::class, cascade: ['remove'])]
    private Collection $subscribers;

    // ============================================
    // RELATIONS MESSAGES
    // ============================================

    // Messages envoyés
    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class, cascade: ['remove'])]
    private Collection $sentMessages;

    // Messages reçus
    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class, cascade: ['remove'])]
    private Collection $receivedMessages;

    // ============================================
    // RELATIONS NOTIFICATIONS
    // ============================================
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, cascade: ['remove'])]
    private Collection $notifications;

    // ============================================
    // CONSTRUCTEUR
    // ============================================
    public function __construct()
    {
        $this->favorites = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    // ============================================
    // INTERFACE USERINTERFACE
    // ============================================
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_' . strtoupper($this->role)];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function eraseCredentials(): void {}

    // ============================================
    // GETTERS / SETTERS BASIQUES
    // ============================================
    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): self { $this->role = $role; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function getCin(): ?string { return $this->cin; }
    public function setCin(?string $cin): self { $this->cin = $cin; return $this; }

    public function isValidated(): bool { return $this->isValidated; }
    public function setIsValidated(bool $isValidated): self { $this->isValidated = $isValidated; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }

    public function getReadableRole(): string { return ucfirst(strtolower($this->role)); }

    // ============================================
    // GESTION FAVORITES
    // ============================================
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Business $business): self
    {
        if (!$this->favorites->contains($business)) {
            $this->favorites->add($business);
        }
        return $this;
    }

    public function removeFavorite(Business $business): self
    {
        $this->favorites->removeElement($business);
        return $this;
    }

    // ============================================
    // GESTION SUBSCRIPTIONS
    // ============================================

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setSubscriber($this);
        }
        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            if ($subscription->getSubscriber() === $this) {
                $subscription->setSubscriber(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function addSubscriber(Subscription $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers->add($subscriber);
            $subscriber->setProvider($this);
        }
        return $this;
    }

    public function removeSubscriber(Subscription $subscriber): self
    {
        if ($this->subscribers->removeElement($subscriber)) {
            if ($subscriber->getProvider() === $this) {
                $subscriber->setProvider(null);
            }
        }
        return $this;
    }

    /**
     * Vérifie si l'utilisateur est abonné à un provider
     */
    public function isSubscribedTo(User $provider): bool
    {
        foreach ($this->subscriptions as $subscription) {
            if ($subscription->getProvider() === $provider && $subscription->isActive()) {
                return true;
            }
        }
        return false;
    }

    // ============================================
    // GESTION MESSAGES
    // ============================================

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $message): self
    {
        if (!$this->sentMessages->contains($message)) {
            $this->sentMessages->add($message);
            $message->setSender($this);
        }
        return $this;
    }

    public function removeSentMessage(Message $message): self
    {
        if ($this->sentMessages->removeElement($message)) {
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $message): self
    {
        if (!$this->receivedMessages->contains($message)) {
            $this->receivedMessages->add($message);
            $message->setReceiver($this);
        }
        return $this;
    }

    public function removeReceivedMessage(Message $message): self
    {
        if ($this->receivedMessages->removeElement($message)) {
            if ($message->getReceiver() === $this) {
                $message->setReceiver(null);
            }
        }
        return $this;
    }

    /**
     * Compte les messages non lus
     */
    public function getUnreadMessagesCount(): int
    {
        $count = 0;
        foreach ($this->receivedMessages as $message) {
            if (!$message->isRead()) {
                $count++;
            }
        }
        return $count;
    }

    // ============================================
    // GESTION NOTIFICATIONS
    // ============================================

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }
        return $this;
    }

    /**
     * Compte les notifications non lues
     */
    public function getUnreadNotificationsCount(): int
    {
        $count = 0;
        foreach ($this->notifications as $notification) {
            if (!$notification->isRead()) {
                $count++;
            }
        }
        return $count;
    }
}