<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211016150232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE basket (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, products_id LONGTEXT NOT NULL, quantity LONGTEXT NOT NULL, shop_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_64C19C14D16C4DD (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, basket_id INT NOT NULL, buyer_id INT NOT NULL, shop_id INT NOT NULL, day DATE NOT NULL, time_begin TIME DEFAULT NULL, time_end TIME DEFAULT NULL, products_id LONGTEXT NOT NULL, quantity LONGTEXT NOT NULL, status INT NOT NULL, total DOUBLE PRECISION NOT NULL, number VARCHAR(12) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, deal_price DOUBLE PRECISION DEFAULT NULL, deal_start DATETIME DEFAULT NULL, deal_end DATETIME DEFAULT NULL, images JSON NOT NULL, deleted_at DATETIME DEFAULT NULL, status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, address LONGTEXT NOT NULL, postal_code INT NOT NULL, city VARCHAR(255) NOT NULL, phone INT DEFAULT NULL, email VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, banned TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, cover LONGTEXT NOT NULL, tag INT NOT NULL, UNIQUE INDEX UNIQ_AC6A4CA25E237E06 (name), UNIQUE INDEX UNIQ_AC6A4CA2989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_payment (shop_id INT NOT NULL, payment_id INT NOT NULL, INDEX IDX_6E1BC4274D16C4DD (shop_id), INDEX IDX_6E1BC4274C3A3BB (payment_id), PRIMARY KEY(shop_id, payment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_table (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, mon_am_op TIME DEFAULT NULL, mon_am_cl TIME DEFAULT NULL, mon_pm_op TIME DEFAULT NULL, mon_pm_cl TIME DEFAULT NULL, tue_am_op TIME DEFAULT NULL, tue_am_cl TIME DEFAULT NULL, tue_pm_op TIME DEFAULT NULL, tue_pm_cl TIME DEFAULT NULL, wed_am_op TIME DEFAULT NULL, wed_am_cl TIME DEFAULT NULL, wed_pm_op TIME DEFAULT NULL, wed_pm_cl TIME DEFAULT NULL, thu_am_op TIME DEFAULT NULL, thu_am_cl TIME DEFAULT NULL, thu_pm_op TIME DEFAULT NULL, thu_pm_cl TIME DEFAULT NULL, fri_am_op TIME DEFAULT NULL, fri_am_cl TIME DEFAULT NULL, fri_pm_op TIME DEFAULT NULL, fri_pm_cl TIME DEFAULT NULL, sat_am_op TIME DEFAULT NULL, sat_am_cl TIME DEFAULT NULL, sat_pm_op TIME DEFAULT NULL, sat_pm_cl TIME DEFAULT NULL, sun_am_op TIME DEFAULT NULL, sun_am_cl TIME DEFAULT NULL, sun_pm_op TIME DEFAULT NULL, sun_pm_cl TIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(180) NOT NULL, roles JSON NOT NULL, password LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, postal_code INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, avatar LONGTEXT DEFAULT NULL, banned TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649DBC463C4 (full_name), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C14D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shop_payment ADD CONSTRAINT FK_6E1BC4274D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shop_payment ADD CONSTRAINT FK_6E1BC4274C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_payment DROP FOREIGN KEY FK_6E1BC4274C3A3BB');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C14D16C4DD');
        $this->addSql('ALTER TABLE shop_payment DROP FOREIGN KEY FK_6E1BC4274D16C4DD');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE basket');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE shop_payment');
        $this->addSql('DROP TABLE time_table');
        $this->addSql('DROP TABLE user');
    }
}
