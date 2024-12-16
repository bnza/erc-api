<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240905103953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create views';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE VIEW geom.vw_site AS SELECT site_id AS id, site_id, ST_AsGeoJSON(geom) as geom FROM geom.site;'
        );

        $this->addSql(
            <<<EOF
CREATE VIEW vw_sus_relationships AS
SELECT
id, sx_su_id, relationship_id, dx_su_id FROM sus_relationships
UNION
SELECT sr.id*-1, sr.dx_su_id as sx_su_id, r.inverted_by_id, sr.sx_su_id as dx_su_id FROM sus_relationships sr
LEFT JOIN voc__su__relationship r ON sr.relationship_id::char = r.id::char;
EOF
        );

        $this->addSql(
            <<<EOF
CREATE RULE __insert AS ON INSERT TO vw_sus_relationships DO INSTEAD
INSERT INTO sus_relationships
(
	id,
	sx_su_id,
	dx_su_id,
	relationship_id
)
VALUES
(
    nextval('sus_relationships_id_seq'),
	NEW.sx_su_id,
	NEW.dx_su_id,
	NEW.relationship_id
)
EOF
        );

        $this->addSql(
            'CREATE RULE __delete AS ON DELETE TO vw_sus_relationships DO INSTEAD DELETE FROM sus_relationships WHERE id = ABS(OLD.id)'
        );

        $this->addSql(
            <<<EOF
            CREATE VIEW vw_sus__samples AS
SELECT (id || '_' || su_id) as id,  id as sample_id, su_id FROM sample
UNION
SELECT  (sample_id || '_' || su_id) as id, sample_id, su_id FROM mu
;
EOF
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS geom.vw_site');

        $this->addSql('DROP VIEW IF EXISTS vw_sus_relationships;');

        $this->addSql('DROP VIEW IF EXISTS vw_sus__samples;');
    }
}
