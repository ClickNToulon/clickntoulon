<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210701191200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE shop_id shop_id INT DEFAULT NULL, CHANGE products_id products_id LONGTEXT DEFAULT NULL, CHANGE owner_id owner_id INT DEFAULT NULL, CHANGE quantity quantity LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE basket_id basket_id INT DEFAULT NULL, CHANGE buyer_id buyer_id INT DEFAULT NULL, CHANGE shop_id shop_id INT DEFAULT NULL, CHANGE day day DATE DEFAULT NULL, CHANGE status status INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE owner_id owner_id INT NOT NULL, CHANGE shop_id shop_id INT NOT NULL, CHANGE products_id products_id LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE quantity quantity LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE category CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `order` CHANGE basket_id basket_id INT NOT NULL, CHANGE buyer_id buyer_id INT NOT NULL, CHANGE shop_id shop_id INT NOT NULL, CHANGE day day DATE NOT NULL, CHANGE status status INT NOT NULL');
    }
}
