<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    public const TYPE_FOLLOW = 'follow';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $type;

    #[ORM\Column(length: 255)]
    private string $content;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $recipient;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $sender;

    #[ORM\Column]
    private bool $isRead = false;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(string $type, string $content, User $recipient, User $sender)
    {
        $this->type      = $type;
        $this->content   = $content;
        $this->recipient = $recipient;
        $this->sender    = $sender;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getType(): string { return $this->type; }

    public function getContent(): string { return $this->content; }

    public function getRecipient(): User { return $this->recipient; }

    public function getSender(): User { return $this->sender; }

    public function isRead(): bool { return $this->isRead; }

    public function markAsRead(): static
    {
        $this->isRead = true;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
}
