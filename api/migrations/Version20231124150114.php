<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231124150114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE VIEW geom.vw_site AS SELECT site_id AS id, site_id, geom FROM geom.site;');
        $this->addSql('CREATE VIEW geom.vw_site AS SELECT site_id AS id, site_id, ST_AsGeoJSON(geom) as geom FROM geom.site;');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP VIEW IF EXISTS geom.vw_site');
    }
}
