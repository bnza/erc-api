<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228144051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE zoo_bone_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE zoo_bone (id INT NOT NULL, su_id INT DEFAULT NULL, taxonomy VARCHAR(255) NOT NULL, unidentified BOOLEAN NOT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_72AFEBC3BDB1218E ON zoo_bone (su_id)');
        $this->addSql(
            'ALTER TABLE zoo_bone ADD CONSTRAINT FK_72AFEBC3BDB1218E FOREIGN KEY (su_id) REFERENCES su (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE zoo_bone_id_seq CASCADE');
        $this->addSql('ALTER TABLE zoo_bone DROP CONSTRAINT FK_72AFEBC3BDB1218E');
        $this->addSql('DROP TABLE zoo_bone');
    }
}
