<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250327154534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE job.imported_file (id UUID NOT NULL, media_object_id INT DEFAULT NULL, service VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, upload_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99A3110264DE5A5 ON job.imported_file (media_object_id)');
        $this->addSql('COMMENT ON COLUMN job.imported_file.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN job.imported_file.upload_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'ALTER TABLE job.imported_file ADD CONSTRAINT FK_99A3110264DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE job.imported_file DROP CONSTRAINT FK_99A3110264DE5A5');
        $this->addSql('DROP TABLE job.imported_file');
    }
}
