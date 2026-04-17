<?php

namespace App\Twig;

use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
        private readonly MessageRepository      $messageRepository,
        private readonly Security               $security,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_notifications_count', $this->unreadCount(...)),
            new TwigFunction('unread_messages_count', $this->unreadMessagesCount(...)),
        ];
    }

    public function unreadCount(): int
    {
        $user = $this->security->getUser();

        return $user ? $this->notificationRepository->countUnreadForUser($user) : 0;
    }

    public function unreadMessagesCount(): int
    {
        $user = $this->security->getUser();

        return $user ? $this->messageRepository->countUnreadForUser($user) : 0;
    }
}
