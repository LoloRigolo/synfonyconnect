<?php

namespace App\MessageHandler;

use App\Message\NewMessageNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class NewMessageNotificationHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(NewMessageNotification $notification): void
    {
        $preview = mb_strlen($notification->contentPreview) > 100
            ? mb_substr($notification->contentPreview, 0, 100) . '…'
            : $notification->contentPreview;

        $email = (new Email())
            ->from('no-reply@symfoconnect.local')
            ->to($notification->recipientEmail)
            ->subject(sprintf('[SymfoConnect] Nouveau message de %s', $notification->senderUsername))
            ->html(sprintf(
                <<<HTML
                <!DOCTYPE html>
                <html lang="fr">
                <body style="font-family:sans-serif;background:#f4f6f9;padding:2rem;">
                  <div style="max-width:520px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);">
                    <div style="background:#1a1a2e;padding:1.25rem 2rem;">
                      <h1 style="color:#fff;font-size:1.2rem;margin:0;">SymfoConnect</h1>
                    </div>
                    <div style="padding:2rem;">
                      <p style="color:#1a1a2e;font-size:1rem;margin-top:0;">
                        Bonjour <strong>%s</strong>,
                      </p>
                      <p style="color:#444460;">
                        <strong>%s</strong> vous a envoyé un nouveau message :
                      </p>
                      <blockquote style="border-left:4px solid #6c63ff;margin:1rem 0;padding:.75rem 1rem;background:#f3f1ff;color:#333350;border-radius:0 8px 8px 0;">
                        %s
                      </blockquote>
                      <a href="http://localhost/messages/%s"
                         style="display:inline-block;background:#6c63ff;color:#fff;text-decoration:none;padding:.65rem 1.5rem;border-radius:8px;font-weight:600;margin-top:.5rem;">
                        Répondre
                      </a>
                    </div>
                    <div style="padding:1rem 2rem;border-top:1px solid #e8e8f0;font-size:.8rem;color:#aaa;">
                      Vous recevez cet email car vous êtes inscrit sur SymfoConnect.
                    </div>
                  </div>
                </body>
                </html>
                HTML,
                htmlspecialchars($notification->recipientUsername),
                htmlspecialchars($notification->senderUsername),
                nl2br(htmlspecialchars($preview)),
                urlencode($notification->senderUsername),
            ));

        $this->mailer->send($email);
    }
}
