<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920153323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE sample_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE sample (id INT NOT NULL, su_id INT DEFAULT NULL, number INT NOT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_F10B76C3BDB1218E ON sample (su_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F10B76C3BDB1218E96901F54 ON sample (su_id, number)');
        $this->addSql(
            'ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3BDB1218E FOREIGN KEY (su_id) REFERENCES su (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE sample_id_seq CASCADE');
        $this->addSql('ALTER TABLE sample DROP CONSTRAINT FK_F10B76C3BDB1218E');
        $this->addSql('DROP TABLE sample');
    }
}
