<?php

namespace App\Message;

/**
 * Dispatché de façon asynchrone après l'envoi d'un message privé.
 * Contient uniquement des scalaires pour éviter les problèmes de sérialisation.
 */
final class NewMessageNotification
{
    public function __construct(
        public readonly int    $messageId,
        public readonly string $senderUsername,
        public readonly string $recipientEmail,
        public readonly string $recipientUsername,
        public readonly string $contentPreview,
    ) {}
}
