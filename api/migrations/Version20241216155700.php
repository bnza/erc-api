<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216155700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_b1e582a61b1fea20bdb1218e96901f54');
        $this->addSql('ALTER TABLE mu ADD interpretation TEXT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1E582A61B1FEA2096901F54 ON mu (sample_id, number)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_B1E582A61B1FEA2096901F54');
        $this->addSql('ALTER TABLE mu DROP interpretation');
        $this->addSql('CREATE UNIQUE INDEX uniq_b1e582a61b1fea20bdb1218e96901f54 ON mu (sample_id, su_id, number)');
    }
}
