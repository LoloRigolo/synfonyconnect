<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260417090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add message table for private messaging';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE message (
            id INT AUTO_INCREMENT NOT NULL,
            sender_id INT NOT NULL,
            recipient_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX IDX_message_sender (sender_id),
            INDEX IDX_message_recipient (recipient_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4");

        $this->addSql('ALTER TABLE message
            ADD CONSTRAINT FK_message_sender    FOREIGN KEY (sender_id)    REFERENCES `user` (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_message_recipient FOREIGN KEY (recipient_id) REFERENCES `user` (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_message_sender');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_message_recipient');
        $this->addSql('DROP TABLE message');
    }
}
