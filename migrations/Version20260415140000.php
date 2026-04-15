<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add createdAt to Post';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post ADD created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE post DROP created_at');
    }
}
