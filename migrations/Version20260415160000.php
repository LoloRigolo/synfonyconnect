<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user_follow table for follow/unfollow feature';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_follow (
            follower_id INT NOT NULL,
            followed_id INT NOT NULL,
            INDEX IDX_user_follow_follower (follower_id),
            INDEX IDX_user_follow_followed (followed_id),
            PRIMARY KEY (follower_id, followed_id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE user_follow
            ADD CONSTRAINT FK_user_follow_follower FOREIGN KEY (follower_id) REFERENCES `user` (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_user_follow_followed FOREIGN KEY (followed_id) REFERENCES `user` (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_follow DROP FOREIGN KEY FK_user_follow_follower');
        $this->addSql('ALTER TABLE user_follow DROP FOREIGN KEY FK_user_follow_followed');
        $this->addSql('DROP TABLE user_follow');
    }
}
