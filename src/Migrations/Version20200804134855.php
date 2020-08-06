<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200804134855 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add tables for processing official train-tables (IFF)';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_vervoerder` ADD `iff_code` INT DEFAULT NULL');

        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 500 WHERE `vervoerder_id` = 2');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 600 WHERE `vervoerder_id` = 10');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 310 WHERE `vervoerder_id` = 54');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 911 WHERE `vervoerder_id` = 45');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 962 WHERE `vervoerder_id` = 50');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 920 WHERE `vervoerder_id` = 55');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 100 WHERE `vervoerder_id` = 1');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 200 WHERE `vervoerder_id` = 31');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 750 WHERE `vervoerder_id` = 63');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 980 WHERE `vervoerder_id` = 20');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 400 WHERE `vervoerder_id` = 9');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 300 WHERE `vervoerder_id` = 3');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 700 WHERE `vervoerder_id` = 11');
        $this->addSql('UPDATE `somda_vervoerder` SET `iff_code` = 980 WHERE `vervoerder_id` = 20');

        $this->addSql('
             CREATE TABLE `ofo_official_footnote` (
                `ofo_id` BIGINT AUTO_INCREMENT NOT NULL,
                `ofo_footnote_id` BIGINT NOT NULL,
                `ofo_date` DATETIME NOT NULL,
                UNIQUE INDEX `idx_ofo_footnote` (`ofo_footnote_id`, `ofo_date`),
                PRIMARY KEY(`ofo_id`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE `ott_official_train_table` (
                `ott_id` BIGINT AUTO_INCREMENT NOT NULL,
                `ott_ofo_footnote_id` BIGINT DEFAULT NULL,
                `ott_transporter_id` BIGINT DEFAULT NULL,
                `ott_characteristic_id` BIGINT DEFAULT NULL,
                `ott_route_id` BIGINT DEFAULT NULL,
                `ott_location_id` BIGINT DEFAULT NULL,
                `ott_order` INT DEFAULT 1 NOT NULL,
                `ott_action` VARCHAR(1) DEFAULT \'-\' NOT NULL,
                `ott_time` INT DEFAULT 0 NOT NULL,
                `ott_track` VARCHAR(3) DEFAULT NULL, 
                INDEX `IDX_4577F52EECDD4D74` (`ott_ofo_footnote_id`),
                INDEX `IDX_4577F52E2EDDB7B4` (`ott_transporter_id`),
                INDEX `IDX_4577F52EBE696BF` (`ott_characteristic_id`),
                INDEX `idx_ott_time` (`ott_time`),
                INDEX `idx_ott_location_id` (`ott_location_id`),
                INDEX `idx_ott_route_id` (`ott_route_id`),
                PRIMARY KEY(`ott_id`)
           ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
       ');
        $this->addSql('
            ALTER TABLE `ott_official_train_table` ADD CONSTRAINT `FK_ott_ofo_footnote_id`
            FOREIGN KEY (`ott_ofo_footnote_id`) REFERENCES `ofo_official_footnote` (`ofo_footnote_id`)
        ');
        $this->addSql('
            ALTER TABLE `ott_official_train_table` ADD CONSTRAINT `FK_ott_transporter_id`
            FOREIGN KEY (`ott_transporter_id`) REFERENCES `somda_vervoerder` (`vervoerder_id`)
        ');
        $this->addSql('
            ALTER TABLE `ott_official_train_table` ADD CONSTRAINT `FK_ott_characteristic_id`
            FOREIGN KEY (`ott_characteristic_id`) REFERENCES `somda_karakteristiek` (`karakteristiek_id`)
        ');
        $this->addSql('
            ALTER TABLE `ott_official_train_table` ADD CONSTRAINT `FK_ott_route_id`
            FOREIGN KEY (`ott_route_id`) REFERENCES `somda_trein` (`treinid`)
        ');
        $this->addSql('
            ALTER TABLE `ott_official_train_table` ADD CONSTRAINT `FK_ott_location_id`
            FOREIGN KEY (`ott_location_id`) REFERENCES `somda_verk` (`afkid`)
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
