<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240802081158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_65a4bd79f6bd164696901f54');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65A4BD79F6BD1646BB82733796901F54 ON su (site_id, year, number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_65A4BD79F6BD1646BB82733796901F54');
        $this->addSql('CREATE UNIQUE INDEX uniq_65a4bd79f6bd164696901f54 ON su (site_id, number)');
    }
}
