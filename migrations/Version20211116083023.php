<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211116083023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, code_postal VARCHAR(5) NOT NULL, code_commune VARCHAR(5) NOT NULL, nom_commune VARCHAR(255) NOT NULL, libelle_acheminement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE working_days (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, day VARCHAR(255) NOT NULL, is_closed TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE working_hours (id INT AUTO_INCREMENT NOT NULL, working_days_id INT DEFAULT NULL, time_open DATETIME NOT NULL, time_close DATETIME NOT NULL, INDEX IDX_D72CDC3DEB0558D9 (working_days_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE working_hours ADD CONSTRAINT FK_D72CDC3DEB0558D9 FOREIGN KEY (working_days_id) REFERENCES working_days (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE working_hours DROP FOREIGN KEY FK_D72CDC3DEB0558D9');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE working_days');
        $this->addSql('DROP TABLE working_hours');
    }
}
