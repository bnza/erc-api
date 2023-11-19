<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231119181850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA geom');
        $this->addSql('CREATE SEQUENCE site_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE geom.site_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE site (id INT NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(64) NOT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_694309E45E237E06 ON site (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_694309E477153098 ON site (code)');
        $this->addSql('CREATE TABLE geom.site (id INT NOT NULL, site_id INT DEFAULT NULL, geom geometry(POINTZ, 4326) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B9623109F6BD1646 ON geom.site (site_id)');
        $this->addSql('CREATE INDEX IDX_B962310918CF73E0 ON geom.site USING gist(geom)');
        $this->addSql('CREATE TABLE sites__users (id UUID NOT NULL, site_id INT NOT NULL, user_id UUID NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_32F53B24F6BD1646 ON sites__users (site_id)');
        $this->addSql('CREATE INDEX IDX_32F53B24A76ED395 ON sites__users (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32F53B24F6BD1646A76ED395 ON sites__users (site_id, user_id)');
        $this->addSql('COMMENT ON COLUMN sites__users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sites__users.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN sites__users.roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles TEXT NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE geom.site ADD CONSTRAINT FK_B9623109F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sites__users ADD CONSTRAINT FK_32F53B24F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sites__users ADD CONSTRAINT FK_32F53B24A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE site_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE geom.site_id_seq CASCADE');
        $this->addSql('ALTER TABLE geom.site DROP CONSTRAINT FK_B9623109F6BD1646');
        $this->addSql('ALTER TABLE sites__users DROP CONSTRAINT FK_32F53B24F6BD1646');
        $this->addSql('ALTER TABLE sites__users DROP CONSTRAINT FK_32F53B24A76ED395');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE geom.site');
        $this->addSql('DROP TABLE sites__users');
        $this->addSql('DROP TABLE "user"');
    }
}
