<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241203165019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE mu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE mu (id INT NOT NULL, su_id INT DEFAULT NULL, sample_id INT DEFAULT NULL, number INT DEFAULT NULL, deposit_type VARCHAR(255) NOT NULL, key_attributes VARCHAR(255) DEFAULT NULL, inclusions_geology INT DEFAULT 0 NOT NULL, inclusions_building_materials INT DEFAULT 0 NOT NULL, inclusions_domestic_refuse INT DEFAULT 0 NOT NULL, inclusions_organic_refuse INT DEFAULT 0 NOT NULL, colour_ppl VARCHAR(255) DEFAULT NULL, colour_xpl VARCHAR(255) DEFAULT NULL, colour_oil VARCHAR(255) DEFAULT NULL, lenticular_platey_peds BOOLEAN DEFAULT false NOT NULL, crumbs_or_granules BOOLEAN DEFAULT false NOT NULL, sa_blocky_peds BOOLEAN DEFAULT false NOT NULL, cracks BOOLEAN DEFAULT false NOT NULL, infillings BOOLEAN DEFAULT false NOT NULL, mesofauna__root_bioturbation INT DEFAULT 0 NOT NULL, earthworm_internal_chamber INT DEFAULT 0 NOT NULL, organic__organo_mineral INT DEFAULT 0 NOT NULL, earthworm_granule INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_B1E582A6BDB1218E ON mu (su_id)');
        $this->addSql('CREATE INDEX IDX_B1E582A61B1FEA20 ON mu (sample_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1E582A61B1FEA20BDB1218E96901F54 ON mu (sample_id, su_id, number)');
        $this->addSql(
            'ALTER TABLE mu ADD CONSTRAINT FK_B1E582A6BDB1218E FOREIGN KEY (su_id) REFERENCES su (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE mu ADD CONSTRAINT FK_B1E582A61B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE mu_id_seq CASCADE');
        $this->addSql('ALTER TABLE mu DROP CONSTRAINT FK_B1E582A6BDB1218E');
        $this->addSql('ALTER TABLE mu DROP CONSTRAINT FK_B1E582A61B1FEA20');
        $this->addSql('DROP TABLE mu');
    }
}
