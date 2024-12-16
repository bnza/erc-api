<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712143020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Base schema setup';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA geom');
        $this->addSql('CREATE SEQUENCE area_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media_object_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pottery_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sample_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE site_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE su_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sus__media_objects_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sus_relationships_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__decoration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__functional_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE voc__p__typology_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE app_user (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles TEXT NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON app_user (email)');
        $this->addSql('COMMENT ON COLUMN app_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_user.roles IS \'(DC2Type:simple_array)\'');
        $this->addSql(
            'CREATE TABLE area (id INT NOT NULL, site_id INT DEFAULT NULL, code VARCHAR(3) NOT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_D7943D68F6BD1646 ON area (site_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7943D68F6BD164677153098 ON area (site_id, code)');
        $this->addSql(
            'CREATE TABLE media_object (id INT NOT NULL, file_path VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, size INT NOT NULL, width SMALLINT DEFAULT NULL, height SMALLINT DEFAULT NULL, sha256 CHAR(64) NOT NULL, upload_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_14D431325CC814F7 ON media_object (sha256)');
        $this->addSql('COMMENT ON COLUMN media_object.upload_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE mu (id INT NOT NULL, su_id INT DEFAULT NULL, sample_id INT DEFAULT NULL, number INT DEFAULT NULL, deposit_type VARCHAR(255) NOT NULL, key_attributes VARCHAR(255) DEFAULT NULL, inclusions_geology INT DEFAULT 0 NOT NULL, inclusions_building_materials INT DEFAULT 0 NOT NULL, inclusions_domestic_refuse INT DEFAULT 0 NOT NULL, inclusions_organic_refuse INT DEFAULT 0 NOT NULL, colour_ppl VARCHAR(255) DEFAULT NULL, colour_xpl VARCHAR(255) DEFAULT NULL, colour_oil VARCHAR(255) DEFAULT NULL, lenticular_platey_peds BOOLEAN DEFAULT false NOT NULL, crumbs_or_granules BOOLEAN DEFAULT false NOT NULL, sa_blocky_peds BOOLEAN DEFAULT false NOT NULL, cracks BOOLEAN DEFAULT false NOT NULL, infillings BOOLEAN DEFAULT false NOT NULL, mesofauna__root_bioturbation INT DEFAULT 0 NOT NULL, earthworm_internal_chamber INT DEFAULT 0 NOT NULL, organic__organo_mineral INT DEFAULT 0 NOT NULL, earthworm_granule INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_B1E582A6BDB1218E ON mu (su_id)');
        $this->addSql('CREATE INDEX IDX_B1E582A61B1FEA20 ON mu (sample_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1E582A61B1FEA20BDB1218E96901F54 ON mu (sample_id, su_id, number)');
        $this->addSql(
            'CREATE TABLE pottery (id INT NOT NULL, su_id INT DEFAULT NULL, voc__p__typology_id INT NOT NULL, voc__p__functional_group_id INT NOT NULL, voc__p__part_id INT DEFAULT NULL, number INT NOT NULL, project_identifier VARCHAR(255) DEFAULT NULL, chronology_lower INT DEFAULT NULL, chronology_upper INT DEFAULT NULL, fragments_number INT DEFAULT 1 NOT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_1A651839BDB1218E ON pottery (su_id)');
        $this->addSql('CREATE INDEX IDX_1A651839B45722E3 ON pottery (voc__p__typology_id)');
        $this->addSql('CREATE INDEX IDX_1A651839B4468908 ON pottery (voc__p__functional_group_id)');
        $this->addSql('CREATE INDEX IDX_1A651839398712AE ON pottery (voc__p__part_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1A651839BDB1218E96901F54 ON pottery (su_id, number)');
        $this->addSql(
            'CREATE TABLE sample (id INT NOT NULL, su_id INT DEFAULT NULL, number INT NOT NULL, collector VARCHAR(255) DEFAULT NULL, taking_date DATE DEFAULT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_F10B76C3BDB1218E ON sample (su_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F10B76C3BDB1218E96901F54 ON sample (su_id, number)');
        $this->addSql('COMMENT ON COLUMN sample.taking_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql(
            'CREATE TABLE geom.site (site_id INT NOT NULL, geom geometry(POINTZ, 4326) NOT NULL, PRIMARY KEY(site_id))'
        );
        $this->addSql('CREATE INDEX IDX_B962310918CF73E0 ON geom.site (geom)');
        $this->addSql(
            'CREATE TABLE site (id INT NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(64) NOT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_694309E45E237E06 ON site (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_694309E477153098 ON site (code)');
        $this->addSql(
            'CREATE TABLE sites__users (id UUID NOT NULL, site_id INT NOT NULL, user_id UUID NOT NULL, privileges SMALLINT DEFAULT 0 NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_32F53B24F6BD1646 ON sites__users (site_id)');
        $this->addSql('CREATE INDEX IDX_32F53B24A76ED395 ON sites__users (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32F53B24F6BD1646A76ED395 ON sites__users (site_id, user_id)');
        $this->addSql('COMMENT ON COLUMN sites__users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sites__users.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql(
            'CREATE TABLE su (id INT NOT NULL, site_id INT NOT NULL, area_id INT DEFAULT NULL, number INT NOT NULL, year INT NOT NULL, description TEXT DEFAULT NULL, interpretation TEXT DEFAULT NULL, public BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_65A4BD79F6BD1646 ON su (site_id)');
        $this->addSql('CREATE INDEX IDX_65A4BD79BD0F409C ON su (area_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65A4BD79F6BD1646BB82733796901F54 ON su (site_id, year, number)');
        $this->addSql(
            'CREATE TABLE sus__media_objects (id INT NOT NULL, su_id INT NOT NULL, media_object_id INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_5576F78FBDB1218E ON sus__media_objects (su_id)');
        $this->addSql('CREATE INDEX IDX_5576F78F64DE5A5 ON sus__media_objects (media_object_id)');
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_5576F78FBDB1218E64DE5A5 ON sus__media_objects (su_id, media_object_id)'
        );
        $this->addSql(
            'CREATE TABLE sus_relationships (id INT NOT NULL, sx_su_id INT NOT NULL, dx_su_id INT NOT NULL, relationship_id CHAR(1) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_FFCCEBDE1E31BBE7 ON sus_relationships (sx_su_id)');
        $this->addSql('CREATE INDEX IDX_FFCCEBDE684F83D5 ON sus_relationships (dx_su_id)');
        $this->addSql('CREATE INDEX IDX_FFCCEBDE2C41D668 ON sus_relationships (relationship_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FFCCEBDE1E31BBE7684F83D5 ON sus_relationships (sx_su_id, dx_su_id)');
        $this->addSql(
            'CREATE TABLE voc__p__decoration (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_55DC87201D775834 ON voc__p__decoration (value)');
        $this->addSql(
            'CREATE TABLE voc__p__functional_group (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_672108731D775834 ON voc__p__functional_group (value)');
        $this->addSql('CREATE TABLE voc__p__part (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AB0BCDF21D775834 ON voc__p__part (value)');
        $this->addSql('CREATE TABLE voc__p__typology (id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D318C1D1D775834 ON voc__p__typology (value)');
        $this->addSql(
            'CREATE TABLE voc__su__relationship (id CHAR(1) NOT NULL, inverted_by_id CHAR(1) DEFAULT NULL, value VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB01D41C4CDAD40 ON voc__su__relationship (inverted_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB01D411D775834 ON voc__su__relationship (value)');
        $this->addSql(
            'ALTER TABLE area ADD CONSTRAINT FK_D7943D68F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE mu ADD CONSTRAINT FK_B1E582A6BDB1218E FOREIGN KEY (su_id) REFERENCES su (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE mu ADD CONSTRAINT FK_B1E582A61B1FEA20 FOREIGN KEY (sample_id) REFERENCES sample (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE pottery ADD CONSTRAINT FK_1A651839BDB1218E FOREIGN KEY (su_id) REFERENCES su (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE pottery ADD CONSTRAINT FK_1A651839B45722E3 FOREIGN KEY (voc__p__typology_id) REFERENCES voc__p__typology (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE pottery ADD CONSTRAINT FK_1A651839B4468908 FOREIGN KEY (voc__p__functional_group_id) REFERENCES voc__p__functional_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE pottery ADD CONSTRAINT FK_1A651839398712AE FOREIGN KEY (voc__p__part_id) REFERENCES voc__p__part (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sample ADD CONSTRAINT FK_F10B76C3BDB1218E FOREIGN KEY (su_id) REFERENCES su (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE geom.site ADD CONSTRAINT FK_B9623109F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sites__users ADD CONSTRAINT FK_32F53B24F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sites__users ADD CONSTRAINT FK_32F53B24A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE su ADD CONSTRAINT FK_65A4BD79F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE su ADD CONSTRAINT FK_65A4BD79BD0F409C FOREIGN KEY (area_id) REFERENCES area (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sus__media_objects ADD CONSTRAINT FK_5576F78FBDB1218E FOREIGN KEY (su_id) REFERENCES su (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sus__media_objects ADD CONSTRAINT FK_5576F78F64DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sus_relationships ADD CONSTRAINT FK_FFCCEBDE1E31BBE7 FOREIGN KEY (sx_su_id) REFERENCES su (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sus_relationships ADD CONSTRAINT FK_FFCCEBDE684F83D5 FOREIGN KEY (dx_su_id) REFERENCES su (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE sus_relationships ADD CONSTRAINT FK_FFCCEBDE2C41D668 FOREIGN KEY (relationship_id) REFERENCES voc__su__relationship (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE voc__su__relationship ADD CONSTRAINT FK_2DB01D41C4CDAD40 FOREIGN KEY (inverted_by_id) REFERENCES voc__su__relationship (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE area_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media_object_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pottery_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sample_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE site_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE su_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sus__media_objects_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sus_relationships_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__decoration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__functional_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE voc__p__typology_id_seq CASCADE');
        $this->addSql('ALTER TABLE area DROP CONSTRAINT FK_D7943D68F6BD1646');
        $this->addSql('ALTER TABLE mu DROP CONSTRAINT FK_B1E582A6BDB1218E');
        $this->addSql('ALTER TABLE mu DROP CONSTRAINT FK_B1E582A61B1FEA20');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839BDB1218E');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839B45722E3');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839B4468908');
        $this->addSql('ALTER TABLE pottery DROP CONSTRAINT FK_1A651839398712AE');
        $this->addSql('ALTER TABLE sample DROP CONSTRAINT FK_F10B76C3BDB1218E');
        $this->addSql('ALTER TABLE geom.site DROP CONSTRAINT FK_B9623109F6BD1646');
        $this->addSql('ALTER TABLE sites__users DROP CONSTRAINT FK_32F53B24F6BD1646');
        $this->addSql('ALTER TABLE sites__users DROP CONSTRAINT FK_32F53B24A76ED395');
        $this->addSql('ALTER TABLE su DROP CONSTRAINT FK_65A4BD79F6BD1646');
        $this->addSql('ALTER TABLE su DROP CONSTRAINT FK_65A4BD79BD0F409C');
        $this->addSql('ALTER TABLE sus__media_objects DROP CONSTRAINT FK_5576F78FBDB1218E');
        $this->addSql('ALTER TABLE sus__media_objects DROP CONSTRAINT FK_5576F78F64DE5A5');
        $this->addSql('ALTER TABLE sus_relationships DROP CONSTRAINT FK_FFCCEBDE1E31BBE7');
        $this->addSql('ALTER TABLE sus_relationships DROP CONSTRAINT FK_FFCCEBDE684F83D5');
        $this->addSql('ALTER TABLE sus_relationships DROP CONSTRAINT FK_FFCCEBDE2C41D668');
        $this->addSql('ALTER TABLE voc__su__relationship DROP CONSTRAINT FK_2DB01D41C4CDAD40');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP TABLE mu');
        $this->addSql('DROP TABLE pottery');
        $this->addSql('DROP TABLE sample');
        $this->addSql('DROP TABLE geom.site');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE sites__users');
        $this->addSql('DROP TABLE su');
        $this->addSql('DROP TABLE sus__media_objects');
        $this->addSql('DROP TABLE sus_relationships');
        $this->addSql('DROP TABLE voc__p__decoration');
        $this->addSql('DROP TABLE voc__p__functional_group');
        $this->addSql('DROP TABLE voc__p__part');
        $this->addSql('DROP TABLE voc__p__typology');
        $this->addSql('DROP TABLE voc__su__relationship');

    }
}
