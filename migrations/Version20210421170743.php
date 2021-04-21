<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421170743 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, basket_id INT NOT NULL, buyer_id INT NOT NULL, shop_id INT NOT NULL, day DATE NOT NULL, time_begin TIME DEFAULT NULL, time_end TIME DEFAULT NULL, products_id LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', quantity LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B4D16C4DD');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B6C8A81A9');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B7E3C61F9');
        $this->addSql('DROP INDEX IDX_2246507B4D16C4DD ON basket');
        $this->addSql('DROP INDEX IDX_2246507B6C8A81A9 ON basket');
        $this->addSql('DROP INDEX IDX_2246507B7E3C61F9 ON basket');
        $this->addSql('ALTER TABLE basket CHANGE products_id products_id LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE quantity quantity LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `order`');
        $this->addSql('ALTER TABLE basket CHANGE products_id products_id INT NOT NULL, CHANGE quantity quantity INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B6C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2246507B4D16C4DD ON basket (shop_id)');
        $this->addSql('CREATE INDEX IDX_2246507B6C8A81A9 ON basket (products_id)');
        $this->addSql('CREATE INDEX IDX_2246507B7E3C61F9 ON basket (owner_id)');
    }
}
