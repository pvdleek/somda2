<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111302 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Cleanup users';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // Dubbele gebruikersnamen: SELECT u1.uid, u1.username, u2.uid, u2.username FROM somda_users u1 JOIN somda_users u2 ON LOWER(u1.username) = LOWER(u2.username) AND u1.uid <> u2.uid ORDER BY u1.uid ASC
        // Dubbele e-mailadressen: SELECT u1.uid, u1.email, u2.uid, u2.email FROM somda_users u1 JOIN somda_users u2 ON LOWER(u1.email) = LOWER(u2.email) AND u1.uid <> u2.uid AND u1.email <> '' ORDER BY u1.uid ASC
        $cleanupUserIds = [
            17 => 520,
            139 => 595,
            155 => 1353,
            206 => 9763,
            397 => 568,
            382 => 6068,
            469 => 1125,
            603 => 707,
            677 => 9750,
            683 => 9734,
            795 => 834,
            844 => 4883,
            897 => 9850,
            1372 => 395,
            1499 => 4136,
            1600 => 4954,
            1717 => 9821,
            2073 => 7195,
            2123 => 10037,
            2298 => 816,
            2559 => 6876,
            2627 => 7559,
            2870 => 5294,
            2918 => 2950,
            3035 => 4284,
            3148 => 5531,
            3175 => 4324,
            3206 => 9762,
            3224 => 9741,
            3450 => 4933,
            3578 => 4734,
            4451 => 4546,
            4505 => 4136,
            4565 => 6537,
            4615 => 6610,
            4656 => 8923,
            4714 => 3478,
            4837 => 0,
            4849 => 5068,
            5078 => 6539,
            5112 => 5113,
            5146 => 3457,
            5314 => 5938,
            5344 => 3571,
            5366 => 6059,
            5495 => 1748,
            5609 => 5829,
            5658 => 5659,
            5694 => 6292,
            5754 => 44,
            5808 => 7887,
            6020 => 6474,
            6218 => 8909,
            6291 => 8276,
            6395 => 6959,
            6398 => 6818,
            6467 => 7064,
            6653 => 6190,
            6783 => 7001,
            7004 => 7337,
            7055 => 8414,
            7146 => 9781,
            7149 => 7526,
            7447 => 9735,
            7521 => 8906,
            7524 => 214,
            7610 => 8909,
            7716 => 8745,
            7769 => 9756,
            7990 => 9849,
            8010 => 9430,
            8472 => 5983,
            8494 => 11657,
            8575 => 334,
            8758 => 7711,
            8844 => 0,
            9027 => 6175,
            9085 => 0,
            9184 => 9738,
            9293 => 9295,
            9401 => 9910,
            9455 => 9818,
            9567 => 451,
            9636 => 7809,
            9695 => 7485,
            9850 => 3982,
            9865 => 10019,
            9894 => 9895,
            9913 => 10670,
            9921 => 10064,
            10050 => 10777,
            10864 => 10933,
            10930 => 10944,
            11039 => 11177,
            11143 => 11145,
            11160 => 20422,
            11357 => 11359,
            11355 => 11668,
            11492 => 12872,
            11512 => 11696,
            11593 => 11678,
            11688 => 10452,
            11694 => 583,
            11660 => 1434,
            11682 => 1905,
            11674 => 2208,
            11812 => 3952,
        ];
        foreach ($cleanupUserIds as $oldId => $newId) {
            if ($newId > 0) {
                $this->addSql('UPDATE somda_don_donatie SET don_uid = ' . $newId . ' WHERE don_uid = ' . $oldId);
                $this->addSql('UPDATE somda_forum_alerts SET senderid = ' . $newId . ' WHERE senderid = ' . $oldId);
                $this->addSql('UPDATE somda_forum_discussion SET authorid = ' . $newId . ' WHERE authorid = ' . $oldId);
                $this->addSql('UPDATE somda_forum_favorites SET uid = ' . $newId . ' WHERE uid = ' . $oldId);
                $this->addSql('UPDATE somda_forum_posts SET authorid = ' . $newId . ' WHERE authorid = ' . $oldId);
                $this->addSql('UPDATE somda_forum_posts SET edit_uid = ' . $newId . ' WHERE edit_uid = ' . $oldId);
                $this->addSql('UPDATE somda_mobiel_logging SET uid = ' . $newId . ' WHERE uid = ' . $oldId);
                $this->addSql('UPDATE somda_sht_shout SET sht_uid = ' . $newId . ' WHERE sht_uid = ' . $oldId);
                $this->addSql('UPDATE somda_spots SET uid = ' . $newId . ' WHERE uid = ' . $oldId);
            }
            $this->addSql('DELETE FROM somda_ipb_ip_bans WHERE ipb_uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_poll_votes WHERE uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_users_groups WHERE uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_users_info WHERE uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_users_lastvisit WHERE uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_users_onlineid WHERE uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_users_prefs WHERE uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_news_read WHERE uid = ' . $oldId);
            $this->addSql('DELETE FROM somda_users WHERE uid = ' . $oldId);
        }

        $this->addSql('ALTER TABLE somda_users ADD username_canonical VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users ADD email_canonical VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users ADD enabled BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users ADD salt VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users ADD last_login TIMESTAMP NULL DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users ADD confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users ADD password_requested_at TIMESTAMP NULL DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users ADD roles TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE somda_users CHANGE COLUMN username username VARCHAR(180)');
        $this->addSql('ALTER TABLE somda_users CHANGE COLUMN password password VARCHAR(255)');
        $this->addSql('ALTER TABLE somda_users CHANGE COLUMN email email VARCHAR(180)');

        $this->addSql('UPDATE somda_users SET enabled = active');
        $this->addSql('
            UPDATE somda_users SET enabled = FALSE, email = CONCAT(\'onbekend_\', uid, \'@somda.nl\')
            WHERE email = \'\'
        ');

        $this->addSql('UPDATE somda_users SET username_canonical = LOWER(username)');
        $this->addSql('UPDATE somda_users SET email_canonical = LOWER(email)');
        $this->addSql('UPDATE somda_users SET password = \'\'');
        $this->addSql('UPDATE somda_users SET roles = \'a:1:{i:0;s:9:"ROLE_USER";}\'');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79EBDD692FC23A8 ON somda_users (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79EBDD6A0D96FBF ON somda_users (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79EBDD6C05FB297 ON somda_users (confirmation_token)');

        $this->addSql('
            ALTER TABLE somda_users
            CHANGE COLUMN username_canonical username_canonical VARCHAR(180) DEFAULT \'\' NOT NULL
        ');
        $this->addSql(
            'ALTER TABLE somda_users CHANGE COLUMN email_canonical email_canonical VARCHAR(180) DEFAULT \'\' NOT NULL'
        );
        $this->addSql('ALTER TABLE somda_users CHANGE COLUMN enabled enabled BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('ALTER TABLE somda_users CHANGE COLUMN roles roles TEXT NOT NULL');
        $this->addSql('ALTER TABLE somda_users DROP COLUMN active');

        $this->addSql('ALTER TABLE somda_groups ADD roles TEXT DEFAULT NULL;');
        $this->addSql('ALTER TABLE somda_groups CHANGE COLUMN `name` `name` VARCHAR(180);');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_46C809555E237E06 ON somda_groups (name);');

        $this->addSql('UPDATE somda_groups SET roles = \'a:0:{}\'');
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:10:"ROLE_ADMIN";}\' WHERE groupid = 7');
        $this->addSql('UPDATE somda_groups SET roles = \'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}\' WHERE groupid = 1');

        $this->addSql('ALTER TABLE somda_groups CHANGE COLUMN roles roles TEXT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
