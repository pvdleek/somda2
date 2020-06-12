<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200419080728 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Change URL to route in blocks';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE `somda_blokken`
            DROP `url`,
            DROP `sitemap_last_update`,
            DROP `sitemap_frequency`,
            DROP `sitemap_prio`,
            CHANGE `url_short` `route` VARCHAR(45) NOT NULL
        ');

        $this->addSql('UPDATE `somda_blokken` SET `route` = \'login\' WHERE `blokid` = 4');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'location\' WHERE `blokid` = 6');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_route_lists\' WHERE `blokid` = 7');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_train_tables_input\' WHERE `blokid` = 8');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'train_table\' WHERE `blokid` = 9');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'passing_routes\' WHERE `blokid` = 12');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'train_compositions\' WHERE `blokid` = 14');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'jargon\' WHERE `blokid` = 15');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'advertise\' WHERE `blokid` = 16');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'spot_input\' WHERE `blokid` = 17');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'help\' WHERE `blokid` = 19');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'news\' WHERE `blokid` = 20');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'special_routes\' WHERE `blokid` = 21');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'spots\' WHERE `blokid` = 22');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'contact\' WHERE `blokid` = 23');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'change_password\' WHERE `blokid` = 24');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'lost_password\' WHERE `blokid` = 25');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'register\' WHERE `blokid` = 27');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_user\' WHERE `blokid` = 30');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_news\' WHERE `blokid` = 32');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_special_routes\' WHERE `blokid` = 33');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'settings\' WHERE `blokid` = 35');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'my_spots\' WHERE `blokid` = 38');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'uic\' WHERE `blokid` = 39');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'forum\' WHERE `blokid` = 40');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'pois\' WHERE `blokid` = 41');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_banners\' WHERE `blokid` = 42');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_train_tables\' WHERE `blokid` = 45');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'ddar_trains\' WHERE `blokid` = 48');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_pois\' WHERE `blokid` = 49');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'disclaimer\' WHERE `blokid` = 53');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'logout\' WHERE `blokid` = 54');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'statistics\' WHERE `blokid` = 55');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'about\' WHERE `blokid` = 57');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'forum_favorites\' WHERE `blokid` = 59');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'feeds\' WHERE `blokid` = 60');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'forum_search\' WHERE `blokid` = 64');
        $this->addSql(
            'UPDATE `somda_blokken` SET `route` = \'forum_discussion_post_alerts_overview\' WHERE `blokid` = 70'
        );
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_rail_news\' WHERE `blokid` = 66');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_bans\' WHERE `blokid` = 67');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_ddar_trains\' WHERE `blokid` = 95');
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'manage_train_compositions\' WHERE `blokid` = 98');

        $this->addSql('
            UPDATE `somda_blokken`
            SET `route` = \'\'
            WHERE `blokid` IN (31, 36, 44, 47, 56, 62, 65, 69, 80, 81, 82, 83, 84, 85, 87, 88, 90)
        ');

        $this->addSql('
            DELETE FROM `somda_help_text`
            WHERE `blokid` IN (5, 10, 11, 13, 26, 29, 34, 37, 43, 46, 50, 51, 52, 68, 96)
        ');
        $this->addSql('
            DELETE FROM `somda_stats_blokken`
            WHERE `blokid` IN (5, 10, 11, 13, 26, 29, 34, 37, 43, 46, 50, 51, 52, 68, 96)
        ');
        $this->addSql('
            DELETE FROM `somda_blokken`
            WHERE `blokid` IN (5, 10, 11, 13, 26, 29, 34, 37, 43, 46, 50, 51, 52, 68, 96)
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
