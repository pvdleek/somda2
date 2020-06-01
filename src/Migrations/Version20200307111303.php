<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111303 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add roles to users and groups';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE somda_blokken ADD `role` VARCHAR(50) DEFAULT NULL');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_USER\' WHERE `type` = 3');
        $this->addSql('UPDATE somda_blokken SET `role` = \'IS_AUTHENTICATED_ANONYMOUSLY\' WHERE `type` = 4');

        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ABBREVIATIONS\' WHERE blokid = 6');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_ROUTE_NUMBER_LIST\' WHERE blokid = 7');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_TRAINTABLE_NEW\' WHERE blokid = 8');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_LTI\' WHERE blokid = 11');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_PASSING_ROUTES\' WHERE blokid = 12');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_SPOTS_NEW\' WHERE blokid = 17');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_SPOTS_RECENT\' WHERE blokid = 22');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_USER\' WHERE blokid = 24');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_USERS\' WHERE blokid = 30');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_NEWS\' WHERE blokid = 32');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_SPECIAL_ROUTES\' WHERE blokid = 33');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_USER\' WHERE blokid = 35');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_USER\' WHERE blokid = 36');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_USER\' WHERE blokid = 37');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_SPOTS_EDIT\' WHERE blokid = 38');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_USER\' WHERE blokid = 39');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_BANNERS\' WHERE blokid = 42');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_TRAINTABLE_EDIT\' WHERE blokid = 45');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_TRAINTABLE_ACTIVATE\' WHERE blokid = 46');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_TRAIN_DDAR\' WHERE blokid = 48');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_POI\' WHERE blokid = 49');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_CLEANUP\' WHERE blokid = 51');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ROUTE_OVERVIEW\' WHERE blokid = 52');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_TRAIN_NAMES\' WHERE blokid = 58');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_TRAIN_NAMES\' WHERE blokid = 62');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_RAIL_NEWS\' WHERE blokid = 66');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_BANS\' WHERE blokid = 67');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_USER\' WHERE blokid = 68');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN\' WHERE blokid = 70');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_TRAIN_DDAR\' WHERE blokid = 95');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_POLLS\' WHERE blokid = 96');
        $this->addSql('UPDATE somda_blokken SET `role` = \'ROLE_ADMIN_TRAIN_COMPOSITIONS\' WHERE blokid = 98');
        $this->addSql('DELETE FROM somda_stats_blokken WHERE blokid IN (28, 61, 63, 92, 93, 94, 99)');
        $this->addSql('DELETE FROM somda_logging WHERE blokid IN (28, 61, 63, 92, 93, 94, 99)');
        $this->addSql('DELETE FROM somda_blokken WHERE blokid IN (28, 61, 63, 92, 93, 94, 99)');

        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:13:"ROLE_API_USER";}\' WHERE groupid = 2');
        $this->addSql('
            UPDATE somda_groups SET roles = \'a:6:{i:0;s:18:"ROLE_ABBREVIATIONS";i:1;s:19:' .
            '"ROLE_PASSING_ROUTES";i:2;s:17:"ROLE_SPOTS_RECENT";i:3;s:15:"ROLE_TRAIN_DDAR";i:4;s:19:' .
            '"ROLE_ROUTE_OVERVIEW";i:5;s:16:"ROLE_TRAIN_NAMES";}\' WHERE groupid = 3
        ');
        $this->addSql('
            UPDATE somda_groups SET roles = \'a:8:{i:0;s:18:"ROLE_ABBREVIATIONS";i:1;s:19:' .
            '"ROLE_PASSING_ROUTES";i:2;s:14:"ROLE_SPOTS_NEW";i:3;s:17:"ROLE_SPOTS_RECENT";i:4;s:15:' .
            '"ROLE_SPOTS_EDIT";i:5;s:15:"ROLE_TRAIN_DDAR;i:6;s:19:"ROLE_ROUTE_OVERVIEW";i:7;s:16:' .
            '"ROLE_TRAIN_NAMES";}\' WHERE groupid = 4
        ');
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:16:"ROLE_ADMIN_SPOTS";}\' WHERE groupid = 6');
        $this->addSql('
            UPDATE somda_groups SET roles = \'a:8:{i:0;s:10:"ROLE_ADMIN";i:1;s:15:"ROLE_ADMIN_NEWS";i:2;s:25:' .
            '"ROLE_ADMIN_SPECIAL_ROUTES";i:3;s:30:"ROLE_ADMIN_TRAINTABLE_ACTIVATE";i:4;s:22:' .
            '"ROLE_ADMIN_TRAIN_NAMES";i:5;s:15:"ROLE_ADMIN_BANS";i:6;s:21:"ROLE_ADMIN_TRAIN_DDAR";i:7;s:29:' .
            '"ROLE_ADMIN_TRAIN_COMPOSITIONS";}\' WHERE groupid = 7
        ');
        $this->addSql('
            UPDATE somda_groups SET roles = \'a:3:{i:0;s:28:"ROLE_ADMIN_ROUTE_NUMBER_LIST";i:1;s:25:' .
            '"ROLE_ADMIN_TRAINTABLE_NEW";i:2;s:26:"ROLE_ADMIN_TRAINTABLE_EDIT";}\' WHERE groupid = 9
        ');
        $this->addSql(
            'UPDATE somda_groups SET roles = \'a:1:{i:0;s:30:"ROLE_ADMIN_TRAINTABLE_ACTIVATE";}\' WHERE groupid = 10'
        );
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:18:"ROLE_ADMIN_BANNERS";}\' WHERE groupid = 11');
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:18:"ROLE_ADMIN_BANNERS";}\' WHERE groupid = 12');
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:14:"ROLE_ADMIN_POI";}\' WHERE groupid = 13');
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:20:"ROLE_ADMIN_RAIL_NEWS";}\' WHERE groupid = 18');
        $this->addSql(
            'UPDATE somda_groups SET roles = \'a:1:{i:0;s:25:"ROLE_ADMIN_SPECIAL_ROUTES";}\' WHERE groupid = 19'
        );
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:15:"ROLE_ADMIN_WIKI";}\' WHERE groupid = 20');
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:15:"ROLE_ADMIN_NEWS";}\' WHERE groupid = 21');

        $this->addSql('ALTER TABLE somda_users ADD ban_expire_timestamp DATETIME DEFAULT NULL');
        $this->addSql('
            UPDATE somda_users u
            JOIN somda_users_groups g ON g.uid = u.uid AND g.groupid = 0
            SET ban_expire_timestamp = \'2099-01-01\'
        ');

        $this->addSql('DELETE FROM somda_users_groups WHERE groupid IN (-1, 0, 5, 8, 14)');
        $this->addSql('DELETE FROM somda_groups WHERE groupid IN (-1, 0, 5, 8, 14)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
