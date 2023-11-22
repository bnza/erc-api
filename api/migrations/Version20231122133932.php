<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122133932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sites__users ADD privilege SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE sites__users DROP roles');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE sites__users ADD roles TEXT DEFAULT \'write\' NOT NULL');
        $this->addSql('ALTER TABLE sites__users DROP privilege');
        $this->addSql('COMMENT ON COLUMN sites__users.roles IS \'(DC2Type:simple_array)\'');
    }
}
