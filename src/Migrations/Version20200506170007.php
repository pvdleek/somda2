<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200506170007 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Change all applicable bigint-columns to normal integers and set correct default values';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_banner_hits` ADD `timestamp` DATETIME NOT NULL');
        $this->addSql('UPDATE `somda_banner_hits` SET `timestamp` = FROM_UNIXTIME(`datumtijd`)');
        $this->addSql('ALTER TABLE `somda_banner_hits` DROP `datumtijd`');

        $this->addSql('ALTER TABLE `somda_banner_views` ADD `timestamp` DATETIME NOT NULL');
        $this->addSql('UPDATE `somda_banner_views` SET `timestamp` = FROM_UNIXTIME(`datumtijd`)');
        $this->addSql('ALTER TABLE `somda_banner_views` DROP `datumtijd`');

        $this->addSql('
            ALTER TABLE `somda_tdr`
            CHANGE `orderid` `orderid` INT DEFAULT 1 NOT NULL,
            CHANGE `tijd` `tijd` INT DEFAULT 0 NOT NULL
        ');
        $this->addSql('
            ALTER TABLE `somda_banner`
            CHANGE `max_views` `max_views` INT DEFAULT 0 NOT NULL,
            CHANGE `max_hits` `max_hits` INT DEFAULT 0 NOT NULL
        ');
        $this->addSql('ALTER TABLE `somda_forum_favorites` CHANGE `alerting` `alerting` INT DEFAULT 0 NOT NULL');
        $this->addSql('
            ALTER TABLE `somda_banner_customer`
            CHANGE `max_views` `max_views` INT DEFAULT NULL,
            CHANGE `max_hits` `max_hits` INT DEFAULT NULL,
            CHANGE `max_days` `max_days` INT DEFAULT NULL
        ');
        $this->addSql('ALTER TABLE `somda_tdr_trein_mat` CHANGE `dag` `dag` INT DEFAULT 1 NOT NULL');
        $this->addSql('
            ALTER TABLE `somda_tdr_route`
            CHANGE `type` `type` INT DEFAULT 1 NOT NULL,
            CHANGE `volgorde` `volgorde` INT DEFAULT 1 NOT NULL
        ');
        $this->addSql('ALTER TABLE `somda_forum_log` CHANGE `actie` `actie` INT DEFAULT 0 NOT NULL');
        $this->addSql('
            ALTER TABLE `somda_tdr_treinnummerlijst`
            CHANGE `nr_start` `nr_start` INT DEFAULT 1 NOT NULL,
            CHANGE `nr_eind` `nr_eind` INT DEFAULT 2 NOT NULL
        ');
        $this->addSql('ALTER TABLE `somda_forum_cats` CHANGE `volgorde` `volgorde` INT DEFAULT 1 NOT NULL');
        $this->addSql('
            ALTER TABLE `somda_mat_patterns`
            CHANGE `volgorde` `volgorde` INT DEFAULT 1 NOT NULL,
            CHANGE `pattern` `pattern` VARCHAR(80) DEFAULT \'\' NOT NULL,
            CHANGE `naam` `naam` VARCHAR(50) DEFAULT \'\' NOT NULL
        ');
        $this->addSql('ALTER TABLE `somda_poll_votes` CHANGE `vote` `vote` INT DEFAULT 0 NOT NULL');
        $this->addSql('
            ALTER TABLE `somda_tdr_s_e`
            CHANGE `dag` `dag` INT DEFAULT 1 NOT NULL,
            CHANGE `v_tijd` `v_tijd` INT DEFAULT 0 NOT NULL,
            CHANGE `a_tijd` `a_tijd` INT DEFAULT 0 NOT NULL
        ');
        $this->addSql('
            ALTER TABLE `somda_blokken`
            CHANGE `name` `name` VARCHAR(55) DEFAULT \'\' NOT NULL,
            CHANGE `route` `route` VARCHAR(45) DEFAULT \'\' NOT NULL,
            CHANGE `menu_volgorde` `menu_volgorde` INT DEFAULT 1 NOT NULL
        ');
        $this->addSql('
            ALTER TABLE `somda_forum_forums`
            CHANGE `volgorde` `volgorde` INT DEFAULT 1 NOT NULL,
            CHANGE `type` `type` INT DEFAULT 1 NOT NULL
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
