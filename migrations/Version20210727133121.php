<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210727133121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE property');
        $this->addSql('ALTER TABLE basket CHANGE shop_id shop_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BB852C405 FOREIGN KEY (shop_id_id) REFERENCES shop (id)');
        $this->addSql('CREATE INDEX IDX_2246507BB852C405 ON basket (shop_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price INT NOT NULL, rooms INT NOT NULL, bedrooms INT NOT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, surface INT NOT NULL, floor INT NOT NULL, heat INT NOT NULL, city VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, address VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, postal_code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, sold TINYINT(1) DEFAULT \'0\' NOT NULL, image LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BB852C405');
        $this->addSql('DROP INDEX IDX_2246507BB852C405 ON basket');
        $this->addSql('ALTER TABLE basket CHANGE shop_id_id shop_id INT NOT NULL');
    }
}
