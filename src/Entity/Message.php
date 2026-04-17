<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $sender;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $recipient;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le message ne peut pas être vide.')]
    #[Assert\Length(max: 2000, maxMessage: 'Le message ne peut pas dépasser {{ limit }} caractères.')]
    private string $content;

    #[ORM\Column]
    private bool $isRead = false;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(User $sender, User $recipient, string $content)
    {
        $this->sender    = $sender;
        $this->recipient = $recipient;
        $this->content   = $content;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getSender(): User { return $this->sender; }

    public function getRecipient(): User { return $this->recipient; }

    public function getContent(): string { return $this->content; }

    public function isRead(): bool { return $this->isRead; }

    public function markAsRead(): static
    {
        $this->isRead = true;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
}
