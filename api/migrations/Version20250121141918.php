<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121141918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE potteries__media_objects_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE potteries__media_objects (id INT NOT NULL, pottery_id INT NOT NULL, media_object_id INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_E9D2D624F23816BB ON potteries__media_objects (pottery_id)');
        $this->addSql('CREATE INDEX IDX_E9D2D62464DE5A5 ON potteries__media_objects (media_object_id)');
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_E9D2D624F23816BB64DE5A5 ON potteries__media_objects (pottery_id, media_object_id)'
        );
        $this->addSql(
            'ALTER TABLE potteries__media_objects ADD CONSTRAINT FK_E9D2D624F23816BB FOREIGN KEY (pottery_id) REFERENCES pottery (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE potteries__media_objects ADD CONSTRAINT FK_E9D2D62464DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE potteries__media_objects_id_seq CASCADE');
        $this->addSql('ALTER TABLE potteries__media_objects DROP CONSTRAINT FK_E9D2D624F23816BB');
        $this->addSql('ALTER TABLE potteries__media_objects DROP CONSTRAINT FK_E9D2D62464DE5A5');
        $this->addSql('DROP TABLE potteries__media_objects');
    }
}
