<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231102175445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE public.site_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE public.site (id INT NOT NULL, code VARCHAR(3) NOT NULL, name VARCHAR(64) NOT NULL, description TEXT DEFAULT NULL, public BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D6AC824A5E237E06 ON public.site (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D6AC824A77153098 ON public.site (code)');
        $this->addSql('CREATE TABLE public.sites__users (id UUID NOT NULL, site_id INT NOT NULL, user_id UUID NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_72A64600F6BD1646 ON public.sites__users (site_id)');
        $this->addSql('CREATE INDEX IDX_72A64600A76ED395 ON public.sites__users (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72A64600F6BD1646A76ED395 ON public.sites__users (site_id, user_id)');
        $this->addSql('COMMENT ON COLUMN public.sites__users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN public.sites__users.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN public.sites__users.roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE "public"."user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles TEXT NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_327C5DE7E7927C74 ON "public"."user" (email)');
        $this->addSql('COMMENT ON COLUMN "public"."user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "public"."user".roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE public.sites__users ADD CONSTRAINT FK_72A64600F6BD1646 FOREIGN KEY (site_id) REFERENCES public.site (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.sites__users ADD CONSTRAINT FK_72A64600A76ED395 FOREIGN KEY (user_id) REFERENCES "public"."user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE public.site_id_seq CASCADE');
        $this->addSql('ALTER TABLE public.sites__users DROP CONSTRAINT FK_72A64600F6BD1646');
        $this->addSql('ALTER TABLE public.sites__users DROP CONSTRAINT FK_72A64600A76ED395');
        $this->addSql('DROP TABLE public.site');
        $this->addSql('DROP TABLE public.sites__users');
        $this->addSql('DROP TABLE "public"."user"');
    }
}
