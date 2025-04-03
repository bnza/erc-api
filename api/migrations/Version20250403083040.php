<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250403083040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'SQL'
            CREATE SCHEMA auth
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE SEQUENCE auth.refresh_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE TABLE auth.refresh_token (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B7BCAFB6C74F2195 ON auth.refresh_token (refresh_token)
        SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'SQL'
            DROP SEQUENCE auth.refresh_token_id_seq CASCADE
        SQL
        );
        $this->addSql(
            <<<'SQL'
            DROP TABLE auth.refresh_token
        SQL
        );
    }
}
