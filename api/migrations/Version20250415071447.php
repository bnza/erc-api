<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415071447 extends AbstractMigration
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
            CREATE SEQUENCE samples__media_objects_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE TABLE samples__media_objects (id INT NOT NULL, pottery_id INT NOT NULL, media_object_id INT NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE INDEX IDX_9AFF021BF23816BB ON samples__media_objects (pottery_id)
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE INDEX IDX_9AFF021B64DE5A5 ON samples__media_objects (media_object_id)
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9AFF021BF23816BB64DE5A5 ON samples__media_objects (pottery_id, media_object_id)
        SQL
        );
        $this->addSql(
            <<<'SQL'
            ALTER TABLE samples__media_objects ADD CONSTRAINT FK_9AFF021BF23816BB FOREIGN KEY (pottery_id) REFERENCES sample (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL
        );
        $this->addSql(
            <<<'SQL'
            ALTER TABLE samples__media_objects ADD CONSTRAINT FK_9AFF021B64DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'SQL'
            DROP SEQUENCE samples__media_objects_id_seq CASCADE
        SQL
        );
        $this->addSql(
            <<<'SQL'
            ALTER TABLE samples__media_objects DROP CONSTRAINT FK_9AFF021BF23816BB
        SQL
        );
        $this->addSql(
            <<<'SQL'
            ALTER TABLE samples__media_objects DROP CONSTRAINT FK_9AFF021B64DE5A5
        SQL
        );
        $this->addSql(
            <<<'SQL'
            DROP TABLE samples__media_objects
        SQL
        );
    }
}
