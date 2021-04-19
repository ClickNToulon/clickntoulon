<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210417172711 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE basket (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, products_id INT NOT NULL, owner_id INT NOT NULL, quantity_id INT NOT NULL, INDEX IDX_2246507B4D16C4DD (shop_id), INDEX IDX_2246507B6C8A81A9 (products_id), INDEX IDX_2246507B7E3C61F9 (owner_id), INDEX IDX_2246507B7E8B4AFC (quantity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_payment (shop_id INT NOT NULL, payment_id INT NOT NULL, INDEX IDX_6E1BC4274D16C4DD (shop_id), INDEX IDX_6E1BC4274C3A3BB (payment_id), PRIMARY KEY(shop_id, payment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B6C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B7E8B4AFC FOREIGN KEY (quantity_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE shop_payment ADD CONSTRAINT FK_6E1BC4274D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shop_payment ADD CONSTRAINT FK_6E1BC4274C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop_payment DROP FOREIGN KEY FK_6E1BC4274C3A3BB');
        $this->addSql('DROP TABLE basket');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE shop_payment');
    }
}
