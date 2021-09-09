<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210813222303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE shop_id shop_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BB852C405 FOREIGN KEY (shop_id_id) REFERENCES shop (id)');
        $this->addSql('CREATE INDEX IDX_2246507BB852C405 ON basket (shop_id_id)');
        $this->addSql('ALTER TABLE product ADD images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', DROP image');
        $this->addSql('ALTER TABLE user DROP theme');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BB852C405');
        $this->addSql('DROP INDEX IDX_2246507BB852C405 ON basket');
        $this->addSql('ALTER TABLE basket CHANGE shop_id_id shop_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD image LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP images');
        $this->addSql('ALTER TABLE user ADD theme VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
