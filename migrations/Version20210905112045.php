<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210905112045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your need
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BB852C405');
        $this->addSql('DROP INDEX IDX_2246507BB852C405 ON basket');
        $this->addSql('ALTER TABLE basket CHANGE shop_id_id shop_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD number VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE product ADD images JSON NOT NULL, DROP image');
        $this->addSql('ALTER TABLE user CHANGE password password TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE shop_id shop_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BB852C405 FOREIGN KEY (shop_id_id) REFERENCES shop (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2246507BB852C405 ON basket (shop_id_id)');
        $this->addSql('ALTER TABLE `order` DROP number');
        $this->addSql('ALTER TABLE product ADD image LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP images');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
