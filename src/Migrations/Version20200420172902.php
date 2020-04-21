<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200420172902 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Clean-up of entities';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `somda_drgl_logging`');
        $this->addSql('DROP TABLE `somda_mobiel_logging`');
        $this->addSql('DROP TABLE `somda_users_lastvisit`');
        $this->addSql('DROP TABLE `somda_users_onlineid`');

        $this->addSql('ALTER TABLE `somda_verk` DROP `hafas_code`, DROP `hafas_desc`');
        $this->addSql('ALTER TABLE `somda_users` ADD `last_visit` DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE `somda_logging` CHANGE `datumtijd` `datumtijd` DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `somda_stats` CHANGE `datum` `datum` DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `somda_mat_changes` CHANGE `datum` `datum` DATETIME NOT NULL');

        $this->addSql('ALTER TABLE `somda_spots` DROP FOREIGN KEY `FK_11A6C5C8BC0CC550`');
        $this->addSql('ALTER TABLE `somda_spots` DROP `in_spotid`, CHANGE `datum` `datum` DATETIME NOT NULL');
        $this->addSql('DROP TABLE `somda_in_spots`');
        $this->addSql('
            ALTER TABLE `somda_rijdagen`
            CHANGE `ma` `ma` TINYINT(1) NOT NULL,
            CHANGE `di` `di` TINYINT(1) NOT NULL,
            CHANGE `wo` `wo` TINYINT(1) NOT NULL,
            CHANGE `do` `do` TINYINT(1) NOT NULL,
            CHANGE `vr` `vr` TINYINT(1) NOT NULL,
            CHANGE `za` `za` TINYINT(1) NOT NULL,
            CHANGE `zf` `zf` TINYINT(1) NOT NULL
        ');
        $this->addSql('ALTER TABLE `somda_ddar` CHANGE `matid` `matid` BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE `somda_ddar` ADD CONSTRAINT `FK_9A508BF890261A4`
            FOREIGN KEY (`matid`) REFERENCES `somda_mat` (`matid`)
        ');
        $this->addSql('
            ALTER TABLE `somda_ddar` ADD CONSTRAINT `FK_9A508BFC65F5051`
            FOREIGN KEY (`afkid`) REFERENCES `somda_verk` (`afkid`)
        ');
        $this->addSql('CREATE INDEX `IDX_9A508BFC65F5051` ON `somda_ddar` (`afkid`)');
        $this->addSql('ALTER TABLE `somda_verk_cats` DROP `ns_code`');
        $this->addSql('UPDATE `somda_news` SET `timestamp` = `datum` WHERE `timestamp` = \'2017-12-21 16:18:28\'');
        $this->addSql('ALTER TABLE `somda_news` CHANGE `timestamp` `timestamp` DATETIME NOT NULL, DROP `datum`');
        $this->addSql('ALTER TABLE `somda_sht_shout` CHANGE `sht_datumtijd` `sht_datumtijd` DATETIME NOT NULL');

        $this->addSql('ALTER TABLE `somda_forum_alerts_notes` ADD `timestamp` DATETIME NOT NULL');
        $this->addSql('UPDATE `somda_forum_alerts_notes` SET `timestamp` = CONCAT(`date`, \' \', `time`)');
        $this->addSql('ALTER TABLE `somda_forum_alerts_notes` DROP `date`, DROP `time`');

        $this->addSql('
            ALTER TABLE `somda_banner`
            CHANGE `start_date` `start_date` DATETIME DEFAULT NULL,
            CHANGE `end_date` `end_date` DATETIME DEFAULT NULL
        ');
        $this->addSql('
            UPDATE `somda_banner` SET
            `start_date` = CONCAT(`start_date`, \' \', `start_time`),
            `end_date` = CONCAT(`end_date`, \' \', `end_time`)
        ');
        $this->addSql('ALTER TABLE `somda_banner` DROP `views`, DROP `hits`, DROP `start_time`, DROP `end_time`');

        $this->addSql('ALTER TABLE `somda_forum_alerts` ADD `timestamp` DATETIME NOT NULL');
        $this->addSql('UPDATE `somda_forum_alerts` SET `timestamp` = CONCAT(`date`, \' \', `time`)');
        $this->addSql('ALTER TABLE `somda_forum_alerts` DROP `date`, DROP `time`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
