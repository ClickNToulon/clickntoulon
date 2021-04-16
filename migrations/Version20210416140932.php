<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210416140932 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE time_table (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, mon_am_op TIME DEFAULT NULL, mon_am_cl TIME DEFAULT NULL, mon_pm_op TIME DEFAULT NULL, mon_pm_cl TIME DEFAULT NULL, tue_am_op TIME DEFAULT NULL, tue_am_cl TIME DEFAULT NULL, tue_pm_op TIME DEFAULT NULL, tue_pm_cl TIME DEFAULT NULL, wed_am_op TIME DEFAULT NULL, wed_am_cl TIME DEFAULT NULL, wed_pm_op TIME DEFAULT NULL, wed_pm_cl TIME DEFAULT NULL, thu_am_op TIME DEFAULT NULL, thu_am_cl TIME DEFAULT NULL, thu_pm_op TIME DEFAULT NULL, thu_pm_cl TIME DEFAULT NULL, fri_am_op TIME DEFAULT NULL, fri_am_cl TIME DEFAULT NULL, fri_pm_op TIME DEFAULT NULL, fri_pm_cl TIME DEFAULT NULL, sat_am_op TIME DEFAULT NULL, sat_am_cl TIME DEFAULT NULL, sat_pm_op TIME DEFAULT NULL, sat_pm_cl TIME DEFAULT NULL, sun_am_op TIME DEFAULT NULL, sun_am_cl TIME DEFAULT NULL, sun_pm_op TIME DEFAULT NULL, sun_pm_cl TIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE time_table');
    }
}
