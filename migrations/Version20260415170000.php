<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add post_like table for like/unlike feature';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE post_like (
            post_id INT NOT NULL,
            user_id INT NOT NULL,
            INDEX IDX_post_like_post (post_id),
            INDEX IDX_post_like_user (user_id),
            PRIMARY KEY (post_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE post_like
            ADD CONSTRAINT FK_post_like_post FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_post_like_user FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post_like DROP FOREIGN KEY FK_post_like_post');
        $this->addSql('ALTER TABLE post_like DROP FOREIGN KEY FK_post_like_user');
        $this->addSql('DROP TABLE post_like');
    }
}
