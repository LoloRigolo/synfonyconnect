<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Returns one entry per conversation partner, with the last message and unread count.
     *
     * @return array<int, array{partner: User, lastMessage: Message, unreadCount: int}>
     */
    public function findConversations(User $user): array
    {
        $messages = $this->createQueryBuilder('m')
            ->where('m.sender = :user OR m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $conversations = [];

        foreach ($messages as $message) {
            $partner   = $message->getSender() === $user ? $message->getRecipient() : $message->getSender();
            $partnerId = $partner->getId();

            if (!isset($conversations[$partnerId])) {
                $conversations[$partnerId] = [
                    'partner'     => $partner,
                    'lastMessage' => $message,
                    'unreadCount' => 0,
                ];
            }

            if (!$message->isRead() && $message->getRecipient() === $user) {
                $conversations[$partnerId]['unreadCount']++;
            }
        }

        return array_values($conversations);
    }

    /**
     * @return Message[]
     */
    public function findConversation(User $user, User $other): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user AND m.recipient = :other) OR (m.sender = :other AND m.recipient = :user)')
            ->setParameter('user', $user)
            ->setParameter('other', $other)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countUnreadForUser(User $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.recipient = :user')
            ->andWhere('m.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
