<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add notification table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification (
            id INT AUTO_INCREMENT NOT NULL,
            type VARCHAR(50) NOT NULL,
            content VARCHAR(255) NOT NULL,
            recipient_id INT NOT NULL,
            sender_id INT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_notification_recipient (recipient_id),
            INDEX IDX_notification_sender (sender_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE notification
            ADD CONSTRAINT FK_notification_recipient FOREIGN KEY (recipient_id) REFERENCES `user` (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_notification_sender    FOREIGN KEY (sender_id)    REFERENCES `user` (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_notification_recipient');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_notification_sender');
        $this->addSql('DROP TABLE notification');
    }
}
