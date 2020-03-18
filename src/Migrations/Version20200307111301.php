<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111301 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Cleanup';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM somda_forum_alerts WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_alerts_notes WHERE alertid NOT IN (SELECT id FROM somda_forum_alerts)');
        $this->addSql('DELETE FROM somda_mobiel_logging WHERE uid NOT IN (SELECT uid FROM somda_users)');
        $this->addSql('
            DELETE FROM somda_forum_alerts_notes WHERE alertid IN (
                SELECT id FROM somda_forum_alerts WHERE postid IN (
                    SELECT postid FROM somda_forum_posts WHERE discussionid NOT IN (
                        SELECT discussionid FROM somda_forum_discussion
                    )
                )
            )
        ');
        $this->addSql('
            DELETE FROM somda_forum_alerts WHERE postid IN (
                SELECT postid FROM somda_forum_posts WHERE discussionid NOT IN (
                    SELECT discussionid FROM somda_forum_discussion
                )
            )
        ');
        $this->addSql('
            DELETE FROM somda_forum_zoeken_lijst WHERE post_id IN (
                SELECT postid FROM somda_forum_posts WHERE discussionid NOT IN (
                    SELECT discussionid FROM somda_forum_discussion
                )
            )
        ');
        $this->addSql(
            'DELETE FROM somda_forum_posts WHERE discussionid NOT IN (SELECT discussionid FROM somda_forum_discussion)'
        );
        $this->addSql('DELETE FROM somda_forum_posts_text WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql(
            'UPDATE somda_forum_discussion SET authorid = 0 WHERE authorid NOT IN (SELECT uid FROM somda_users)'
        );
        $this->addSql('UPDATE somda_forum_posts SET authorid = 0 WHERE authorid NOT IN (SELECT uid FROM somda_users)');
        $this->addSql('
            UPDATE somda_forum_posts SET edit_uid = NULL, edit_reason = NULL, edit_date = NULL, edit_time = NULL
            WHERE edit_uid NOT IN (SELECT uid FROM somda_users)
        ');
        $this->addSql('UPDATE somda_mat_naam SET vervoerder_id = NULL WHERE vervoerder_id = 0');
        $this->addSql('ALTER TABLE somda_mat MODIFY vervoerder_id BIGINT(20) DEFAULT NULL');
        $this->addSql('UPDATE somda_mat SET vervoerder_id = NULL WHERE vervoerder_id = 0');
        $this->addSql('DELETE FROM somda_poll_votes WHERE uid NOT IN (SELECT uid FROM somda_users)');
        $this->addSql('DELETE FROM somda_banner_views WHERE bannerid NOT IN (SELECT bannerid FROM somda_banner)');
        $this->addSql('DELETE FROM somda_banner_hits WHERE bannerid NOT IN (SELECT bannerid FROM somda_banner)');
        $this->addSql('DELETE FROM somda_sht_shout WHERE sht_uid NOT IN (SELECT uid FROM somda_users)');
        $this->addSql('ALTER TABLE somda_stats_blokken CHANGE COLUMN `date` `stat_date` DATE');
        $this->addSql('DELETE FROM somda_stats_blokken WHERE blokid NOT IN (SELECT blokid FROM somda_blokken)');
        $this->addSql('
            DELETE FROM somda_forum_discussion_wiki
            WHERE discussionid NOT IN (SELECT discussionid FROM somda_forum_discussion)
        ');

        $this->addSql('DELETE FROM somda_rechten WHERE groupid NOT IN (SELECT groupid FROM somda_groups)');
        $this->addSql('DELETE FROM somda_forum_read_0 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_1 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_2 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_3 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_4 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_5 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_6 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_7 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_8 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_read_9 WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)');
        $this->addSql('DELETE FROM somda_forum_mods WHERE forumid NOT IN (SELECT forumid FROM somda_forum_forums)');
        $this->addSql('UPDATE somda_spots SET in_spotid = NULL');
        $this->addSql('UPDATE somda_spots SET uid = 0 WHERE uid NOT IN (SELECT uid FROM somda_users)');
        $this->addSql('DELETE FROM somda_spots WHERE locatieid NOT IN (SELECT afkid FROM somda_verk)');
        $this->addSql('DELETE FROM somda_spots WHERE matid NOT IN (SELECT matid FROM somda_mat)');
        $this->addSql('DELETE FROM somda_spots WHERE treinid NOT IN (SELECT treinid FROM somda_trein)');
        $this->addSql('DELETE FROM somda_spots_extra WHERE spotid NOT IN (SELECT spotid FROM somda_spots)');
        $this->addSql('DELETE FROM somda_drgl_read WHERE drglid NOT IN (SELECT drglid FROM somda_drgl)');
        $this->addSql(
            'DELETE FROM somda_spots_extra WHERE spotid IN (SELECT spotid FROM somda_spots WHERE locatieid = 1064)'
        );
        $this->addSql('DELETE FROM somda_spots WHERE locatieid = 1064');
        $this->addSql('DELETE FROM somda_verk WHERE landid NOT IN (SELECT verk_catid FROM somda_verk_cats)');
        $this->addSql('DELETE FROM somda_ipb_ip_bans WHERE ipb_uid NOT IN (SELECT uid FROM somda_users)');
        $this->addSql('DELETE FROM somda_drgl_logging WHERE drglid NOT IN (SELECT drglid FROM somda_drgl)');
        $this->addSql('ALTER TABLE somda_users_info MODIFY bedrijf_id BIGINT(20) DEFAULT NULL');
        $this->addSql('
            UPDATE somda_users_info SET bedrijf_id = NULL
            WHERE bedrijf_id NOT IN (SELECT bedrijf_id FROM somda_users_companies)
        ');
        $this->addSql('DELETE FROM somda_users_onlineid WHERE uid NOT IN (SELECT uid FROM somda_users)');
        $this->addSql('DELETE FROM somda_news_read WHERE newsid NOT IN (SELECT newsid FROM somda_news)');
        $this->addSql('DELETE FROM somda_users_groups WHERE groupid NOT IN (SELECT groupid FROM somda_groups)');

        $this->addSql('ALTER TABLE `somda_sns_spoor_nieuws` ADD COLUMN `sns_snb_id` BIGINT(20)');
        $this->addSql('
            UPDATE `somda_sns_spoor_nieuws`
            JOIN `somda_snb_spoor_nieuws_bron` ON `snb_bron` = `sns_bron`
            SET `sns_snb_id` = `snb_id`
        ');
        $this->addSql('
            ALTER TABLE `somda_sns_spoor_nieuws` ADD CONSTRAINT `FRK_sns_snb_id`
            FOREIGN KEY (`sns_snb_id`) REFERENCES `somda_snb_spoor_nieuws_bron`(`snb_id`)
        ');
        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws DROP COLUMN sns_bron');

        $this->addSql('ALTER TABLE somda_news ADD news_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('UPDATE somda_news SET news_timestamp = `timestamp`');
        $this->addSql('ALTER TABLE somda_news DROP `timestamp`');

        $this->addSql('ALTER TABLE somda_banner CHANGE COLUMN `active` `active_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_banner ADD COLUMN active BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_banner SET active = TRUE WHERE active_old = 1');
        $this->addSql('ALTER TABLE somda_banner DROP COLUMN active_old');

        $this->addSql(
            'ALTER TABLE somda_banner_customer_user CHANGE COLUMN `allowed_new` `allowed_new_old` BIGINT(20)'
        );
        $this->addSql('ALTER TABLE somda_banner_customer_user ADD COLUMN allowed_new BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_banner_customer_user SET allowed_new = TRUE WHERE allowed_new_old = 1');
        $this->addSql('ALTER TABLE somda_banner_customer_user DROP COLUMN allowed_new_old');

        $this->addSql('
            ALTER TABLE somda_banner_customer_user CHANGE COLUMN `allowed_max_views` `allowed_max_views_old` BIGINT(20)
        ');
        $this->addSql('ALTER TABLE somda_banner_customer_user ADD COLUMN allowed_max_views BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_banner_customer_user SET allowed_max_views = TRUE WHERE allowed_max_views_old = 1');
        $this->addSql('ALTER TABLE somda_banner_customer_user DROP COLUMN allowed_max_views_old');

        $this->addSql(
            'ALTER TABLE somda_banner_customer_user CHANGE COLUMN `allowed_max_hits` `allowed_max_hits_old` BIGINT(20)'
        );
        $this->addSql('ALTER TABLE somda_banner_customer_user ADD COLUMN allowed_max_hits BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_banner_customer_user SET allowed_max_hits = TRUE WHERE allowed_max_hits_old = 1');
        $this->addSql('ALTER TABLE somda_banner_customer_user DROP COLUMN allowed_max_hits_old');

        $this->addSql(
            'ALTER TABLE somda_banner_customer_user CHANGE COLUMN `allowed_max_date` `allowed_max_date_old` BIGINT(20)'
        );
        $this->addSql('ALTER TABLE somda_banner_customer_user ADD COLUMN allowed_max_date BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_banner_customer_user SET allowed_max_date = TRUE WHERE allowed_max_date_old = 1');
        $this->addSql('ALTER TABLE somda_banner_customer_user DROP COLUMN allowed_max_date_old');

        $this->addSql('
            ALTER TABLE somda_banner_customer_user
            CHANGE COLUMN `allowed_deactivate` `allowed_deactivate_old` BIGINT(20)
        ');
        $this->addSql('ALTER TABLE somda_banner_customer_user ADD COLUMN allowed_deactivate BOOLEAN DEFAULT FALSE');
        $this->addSql(
            'UPDATE somda_banner_customer_user SET allowed_deactivate = TRUE WHERE allowed_deactivate_old = 1'
        );
        $this->addSql('ALTER TABLE somda_banner_customer_user DROP COLUMN allowed_deactivate_old');

        $this->addSql('ALTER TABLE somda_blokken CHANGE COLUMN `do_seperator` `do_seperator_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_blokken ADD COLUMN do_separator BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_blokken SET do_separator = TRUE WHERE do_seperator_old = 1');
        $this->addSql('ALTER TABLE somda_blokken DROP COLUMN do_seperator_old');

        $this->addSql('ALTER TABLE somda_don_donatie CHANGE COLUMN `don_ok` `don_ok_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_don_donatie ADD COLUMN don_ok BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_don_donatie SET don_ok = TRUE WHERE don_ok_old = 1');
        $this->addSql('ALTER TABLE somda_don_donatie DROP COLUMN don_ok_old');

        $this->addSql('ALTER TABLE somda_drgl CHANGE COLUMN `werkzaamheden` `werkzaamheden_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_drgl ADD COLUMN werkzaamheden BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_drgl SET werkzaamheden = TRUE WHERE werkzaamheden_old = 1');
        $this->addSql('ALTER TABLE somda_drgl DROP COLUMN werkzaamheden_old');

        $this->addSql('ALTER TABLE somda_drgl CHANGE COLUMN `public` `public_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_drgl ADD COLUMN public BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_drgl SET public = TRUE WHERE public_old = 1');
        $this->addSql('ALTER TABLE somda_drgl DROP COLUMN public_old');

        $this->addSql('ALTER TABLE somda_forum_alerts CHANGE COLUMN `closed` `closed_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_forum_alerts ADD COLUMN closed BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_forum_alerts SET closed = TRUE WHERE closed_old = 1');
        $this->addSql('ALTER TABLE somda_forum_alerts DROP COLUMN closed_old');

        $this->addSql(
            'ALTER TABLE somda_forum_alerts_notes CHANGE COLUMN `sent_to_reporter` `sent_to_reporter_old` BIGINT(20)'
        );
        $this->addSql('ALTER TABLE somda_forum_alerts_notes ADD COLUMN sent_to_reporter BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_forum_alerts_notes SET sent_to_reporter = TRUE WHERE sent_to_reporter_old = 1');
        $this->addSql('ALTER TABLE somda_forum_alerts_notes DROP COLUMN sent_to_reporter_old');

        $this->addSql('ALTER TABLE somda_forum_discussion CHANGE COLUMN `locked` `locked_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_forum_discussion ADD COLUMN locked BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_forum_discussion SET locked = TRUE WHERE locked_old = 1');
        $this->addSql('ALTER TABLE somda_forum_discussion DROP COLUMN locked_old');

        $this->addSql('ALTER TABLE somda_forum_posts CHANGE COLUMN `sign_on` `sign_on_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_forum_posts ADD COLUMN sign_on BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_forum_posts SET sign_on = TRUE WHERE sign_on_old = 1');
        $this->addSql('ALTER TABLE somda_forum_posts DROP COLUMN sign_on_old');

        $this->addSql('ALTER TABLE somda_forum_zoeken_lijst CHANGE COLUMN `titel` `titel_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_forum_zoeken_lijst ADD COLUMN titel BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_forum_zoeken_lijst SET titel = TRUE WHERE titel_old = 1');
        $this->addSql('ALTER TABLE somda_forum_zoeken_lijst DROP COLUMN titel_old');

        $this->addSql('ALTER TABLE somda_mat_sms CHANGE COLUMN `index_regel` `index_regel_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_mat_sms ADD COLUMN index_regel BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_mat_sms SET index_regel = TRUE WHERE index_regel_old = 1');
        $this->addSql('ALTER TABLE somda_mat_sms DROP COLUMN index_regel_old');

        $this->addSql('ALTER TABLE somda_news CHANGE COLUMN `archief` `archief_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_news ADD COLUMN archief BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_news SET archief = TRUE WHERE archief_old = 1');
        $this->addSql('ALTER TABLE somda_news DROP COLUMN archief_old');

        $this->addSql('ALTER TABLE somda_rijdagen CHANGE COLUMN `ma` `ma_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_rijdagen ADD COLUMN ma BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_rijdagen SET ma = TRUE WHERE ma_old = 1');
        $this->addSql('ALTER TABLE somda_rijdagen DROP COLUMN ma_old');

        $this->addSql('ALTER TABLE somda_rijdagen CHANGE COLUMN `di` `di_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_rijdagen ADD COLUMN di BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_rijdagen SET di = TRUE WHERE di_old = 1');
        $this->addSql('ALTER TABLE somda_rijdagen DROP COLUMN di_old');

        $this->addSql('ALTER TABLE somda_rijdagen CHANGE COLUMN `wo` `wo_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_rijdagen ADD COLUMN wo BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_rijdagen SET wo = TRUE WHERE wo_old = 1');
        $this->addSql('ALTER TABLE somda_rijdagen DROP COLUMN wo_old');

        $this->addSql('ALTER TABLE somda_rijdagen CHANGE COLUMN `do` `do_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_rijdagen ADD COLUMN `do` BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_rijdagen SET `do` = TRUE WHERE do_old = 1');
        $this->addSql('ALTER TABLE somda_rijdagen DROP COLUMN do_old');

        $this->addSql('ALTER TABLE somda_rijdagen CHANGE COLUMN `vr` `vr_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_rijdagen ADD COLUMN vr BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_rijdagen SET vr = TRUE WHERE vr_old = 1');
        $this->addSql('ALTER TABLE somda_rijdagen DROP COLUMN vr_old');

        $this->addSql('ALTER TABLE somda_rijdagen CHANGE COLUMN `za` `za_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_rijdagen ADD COLUMN za BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_rijdagen SET za = TRUE WHERE za_old = 1');
        $this->addSql('ALTER TABLE somda_rijdagen DROP COLUMN za_old');

        $this->addSql('ALTER TABLE somda_rijdagen CHANGE COLUMN `zf` `zf_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_rijdagen ADD COLUMN zf BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_rijdagen SET zf = TRUE WHERE zf_old = 1');
        $this->addSql('ALTER TABLE somda_rijdagen DROP COLUMN zf_old');

        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws CHANGE COLUMN `sns_actief` `sns_actief_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws ADD COLUMN sns_actief BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_sns_spoor_nieuws SET sns_actief = TRUE WHERE sns_actief_old = 1');
        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws DROP COLUMN sns_actief_old');

        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws CHANGE COLUMN `sns_gekeurd` `sns_gekeurd_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws ADD COLUMN sns_gekeurd BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_sns_spoor_nieuws SET sns_gekeurd = TRUE WHERE sns_gekeurd_old = 1');
        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws DROP COLUMN sns_gekeurd_old');

        $this->addSql(
            'ALTER TABLE somda_sns_spoor_nieuws CHANGE COLUMN `sns_bijwerken_ok` `sns_bijwerken_ok_old` BIGINT(20)'
        );
        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws ADD COLUMN sns_bijwerken_ok BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_sns_spoor_nieuws SET sns_bijwerken_ok = TRUE WHERE sns_bijwerken_ok_old = 1');
        $this->addSql('ALTER TABLE somda_sns_spoor_nieuws DROP COLUMN sns_bijwerken_ok_old');

        $this->addSql('ALTER TABLE somda_users CHANGE COLUMN `active` `active_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_users ADD COLUMN active BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_users SET active = TRUE WHERE active_old = 1');
        $this->addSql('ALTER TABLE somda_users DROP COLUMN active_old');

        $this->addSql('ALTER TABLE somda_verk CHANGE COLUMN `spot_allowed` `spot_allowed_old` BIGINT(20)');
        $this->addSql('ALTER TABLE somda_verk ADD COLUMN spot_allowed BOOLEAN DEFAULT FALSE');
        $this->addSql('UPDATE somda_verk SET spot_allowed = TRUE WHERE spot_allowed_old = 1');
        $this->addSql('ALTER TABLE somda_verk DROP COLUMN spot_allowed_old');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
