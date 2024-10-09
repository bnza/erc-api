<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241008130417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE pottery_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__decoration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__functional_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__typology_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE pottery (id INT NOT NULL, su_id INT DEFAULT NULL, voc__p__typology_id INT NOT NULL, voc__p__functional_group_id INT NOT NULL, voc__p__part_id INT DEFAULT NULL, number INT NOT NULL, project_identifier VARCHAR(255) DEFAULT NULL, chronology_lower INT DEFAULT NULL, chronology_upper INT DEFAULT NULL, fragments_number INT DEFAULT 1 NOT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1A651839BDB1218E ON pottery (su_id)');
        $this->addSql('CREATE INDEX IDX_1A651839B45722E3 ON pottery (voc__p__typology_id)');
        $this->addSql('CREATE INDEX IDX_1A651839B4468908 ON pottery (voc__p__functional_group_id)');
        $this->addSql('CREATE INDEX IDX_1A651839398712AE ON pottery (voc__p__part_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1A651839BDB1218E96901F54 ON pottery (su_id, number)');
        $this->addSql('CREATE TABLE voc__p__decoration (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55DC87201D775834 ON voc__p__decoration (value)');
        $this->addSql('CREATE TABLE voc__p__functional_group (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_672108731D775834 ON voc__p__functional_group (value)');
        $this->addSql('CREATE TABLE voc__p__part (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB0BCDF21D775834 ON voc__p__part (value)');
        $this->addSql('CREATE TABLE voc__p__typology (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D318C1D1D775834 ON voc__p__typology (value)');
        $this->addSql('ALTER TABLE pottery ADD CONSTRAINT FK_1A651839BDB1218E FOREIGN KEY (su_id) REFERENCES su (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pottery ADD CONSTRAINT FK_1A651839B45722E3 FOREIGN KEY (voc__p__typology_id) REFERENCES voc__p__typology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pottery ADD CONSTRAINT FK_1A651839B4468908 FOREIGN KEY (voc__p__functional_group_id) REFERENCES voc__p__functional_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pottery ADD CONSTRAINT FK_1A651839398712AE FOREIGN KEY (voc__p__part_id) REFERENCES voc__p__part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE su ALTER public SET DEFAULT true');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE pottery_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__decoration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__functional_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__typology_id_seq CASCADE');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839BDB1218E');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839B45722E3');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839B4468908');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839398712AE');
        $this->addSql('DROP TABLE pottery');
        $this->addSql('DROP TABLE voc__p__decoration');
        $this->addSql('DROP TABLE voc__p__functional_group');
        $this->addSql('DROP TABLE voc__p__part');
        $this->addSql('DROP TABLE voc__p__typology');
        $this->addSql('ALTER TABLE su ALTER public SET DEFAULT false');
    }
}
