<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111308 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Change fields to reflect ORM and create all necessary foreign keys';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE somda_mat CHANGE nummer nummer VARCHAR(20) NOT NULL');
        $this->addSql('
            ALTER TABLE somda_mat ADD CONSTRAINT FK_355CF7922A00C2
            FOREIGN KEY (vervoerder_id) REFERENCES somda_vervoerder (vervoerder_id)
        ');

        $this->addSql('ALTER TABLE somda_positie CHANGE posid posid BIGINT AUTO_INCREMENT NOT NULL');

        $this->addSql('DELETE FROM somda_tdr WHERE treinid NOT IN (SELECT treinid FROM somda_trein)');
        $this->addSql('
            ALTER TABLE somda_tdr
            CHANGE treinid treinid BIGINT DEFAULT NULL,
            CHANGE rijdagenid rijdagenid BIGINT DEFAULT NULL,
            CHANGE locatieid locatieid BIGINT DEFAULT NULL,
            CHANGE tdr_nr tdr_nr BIGINT DEFAULT NULL
        ');
        $this->addSql('
             ALTER TABLE somda_tdr ADD CONSTRAINT FK_84B606F6AE60685A
             FOREIGN KEY (tdr_nr) REFERENCES somda_tdr_drgl (tdr_nr)
         ');
        $this->addSql('
             ALTER TABLE somda_tdr ADD CONSTRAINT FK_84B606F668F454BD
             FOREIGN KEY (treinid) REFERENCES somda_trein (treinid)
         ');
        $this->addSql('
            ALTER TABLE somda_tdr ADD CONSTRAINT FK_84B606F687CF3DBF
            FOREIGN KEY (rijdagenid) REFERENCES somda_rijdagen (rijdagenid)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr ADD CONSTRAINT FK_84B606F6D6E3DC6C
            FOREIGN KEY (locatieid) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('CREATE INDEX IDX_84B606F6AE60685A ON somda_tdr (tdr_nr)');
        $this->addSql('CREATE INDEX IDX_84B606F687CF3DBF ON somda_tdr (rijdagenid)');

        $this->addSql('ALTER TABLE somda_forum_favorites CHANGE alerting alerting BIGINT NOT NULL');
        $this->addSql('
            ALTER TABLE somda_forum_favorites ADD CONSTRAINT FK_4E8B7C93FCC0F19E
            FOREIGN KEY (discussionid) REFERENCES somda_forum_discussion (discussionid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_favorites ADD CONSTRAINT FK_4E8B7C93539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_4E8B7C93FCC0F19E ON somda_forum_favorites (discussionid)');
        $this->addSql('CREATE INDEX IDX_4E8B7C93539B0606 ON somda_forum_favorites (uid)');

        $this->addSql('ALTER TABLE somda_banner_customer CHANGE name name VARCHAR(6) NOT NULL');

        $this->addSql('ALTER TABLE somda_tdr_route DROP PRIMARY KEY');
        $this->addSql('
            ALTER TABLE somda_tdr_route
            CHANGE tdr_nr tdr_nr BIGINT NOT NULL,
            CHANGE locatieid locatieid BIGINT DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_route ADD CONSTRAINT FK_1A52615BAE60685A
            FOREIGN KEY (tdr_nr) REFERENCES somda_tdr_drgl (tdr_nr)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_route ADD CONSTRAINT FK_1A52615B9CBF59B5
            FOREIGN KEY (treinnummerlijst_id) REFERENCES somda_tdr_treinnummerlijst (id)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_route ADD CONSTRAINT FK_1A52615BD6E3DC6C
            FOREIGN KEY (locatieid) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('CREATE INDEX IDX_1A52615BAE60685A ON somda_tdr_route (tdr_nr)');
        $this->addSql('CREATE INDEX IDX_1A52615B9CBF59B5 ON somda_tdr_route (treinnummerlijst_id)');
        $this->addSql('CREATE INDEX IDX_1A52615BD6E3DC6C ON somda_tdr_route (locatieid)');
        $this->addSql('ALTER TABLE somda_tdr_route ADD PRIMARY KEY (tdr_nr, treinnummerlijst_id, type, volgorde)');

        $this->addSql('
            ALTER TABLE somda_verk
            CHANGE afkorting afkorting VARCHAR(10) NOT NULL,
            CHANGE landid landid BIGINT DEFAULT NULL,
            CHANGE hafas_code hafas_code BIGINT DEFAULT NULL,
            CHANGE traject traject VARCHAR(15) DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_verk ADD CONSTRAINT FK_F7F314CE61A61C
            FOREIGN KEY (landid) REFERENCES somda_verk_cats (verk_catid)
        ');

        $this->addSql('ALTER TABLE somda_forum_log CHANGE postid postid BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE somda_forum_log ADD CONSTRAINT FK_256DFB117510F6AF
            FOREIGN KEY (postid) REFERENCES somda_forum_posts (postid)
        ');
        $this->addSql('CREATE INDEX IDX_256DFB117510F6AF ON somda_forum_log (postid)');

        $this->addSql('ALTER TABLE somda_mat_naam CHANGE vervoerder_id vervoerder_id BIGINT DEFAULT NULL');
        $this->addSql('UPDATE `somda_mat_naam` SET vervoerder_id = NULL WHERE vervoerder_id = 0');
        $this->addSql('
            ALTER TABLE somda_mat_naam ADD CONSTRAINT FK_3ADE0A5522A00C2
            FOREIGN KEY (vervoerder_id) REFERENCES somda_vervoerder (vervoerder_id)
        ');
        $this->addSql('CREATE INDEX IDX_3ADE0A5522A00C2 ON somda_mat_naam (vervoerder_id)');

        $this->addSql('
            ALTER TABLE somda_banner_hits
            CHANGE bannerid bannerid BIGINT DEFAULT NULL,
            CHANGE datumtijd datumtijd INT NOT NULL,
            CHANGE ip ip VARCHAR(15) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_banner_hits ADD CONSTRAINT FK_8610F3216BBC5658
            FOREIGN KEY (bannerid) REFERENCES somda_banner (bannerid)
        ');

        $this->addSql('ALTER TABLE somda_karakteristiek CHANGE naam naam VARCHAR(5) NOT NULL');

        $this->addSql('ALTER TABLE somda_tdr_treinnummerlijst CHANGE tdr_nr tdr_nr BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE somda_tdr_treinnummerlijst ADD CONSTRAINT FK_D7A60660AE60685A
            FOREIGN KEY (tdr_nr) REFERENCES somda_tdr_drgl (tdr_nr)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_treinnummerlijst ADD CONSTRAINT FK_D7A6066022A00C2
            FOREIGN KEY (vervoerder_id) REFERENCES somda_vervoerder (vervoerder_id)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_treinnummerlijst ADD CONSTRAINT FK_D7A60660FBDE844F
            FOREIGN KEY (karakteristiek_id) REFERENCES somda_karakteristiek (karakteristiek_id)
        ');
        $this->addSql('CREATE INDEX IDX_D7A60660AE60685A ON somda_tdr_treinnummerlijst (tdr_nr)');
        $this->addSql('CREATE INDEX IDX_D7A6066022A00C2 ON somda_tdr_treinnummerlijst (vervoerder_id)');
        $this->addSql('CREATE INDEX IDX_D7A60660FBDE844F ON somda_tdr_treinnummerlijst (karakteristiek_id)');
        $this->addSql('
            ALTER TABLE somda_tdr_trein_treinnummerlijst ADD CONSTRAINT FK_95ACCAE69CBF59B5
            FOREIGN KEY (treinnummerlijst_id) REFERENCES somda_tdr_treinnummerlijst (id)
        ');
        $this->addSql(
            'DELETE FROM somda_tdr_trein_treinnummerlijst WHERE treinid NOT IN (SELECT treinid FROM somda_trein)'
        );
        $this->addSql('
            ALTER TABLE somda_tdr_trein_treinnummerlijst ADD CONSTRAINT FK_95ACCAE668F454BD
            FOREIGN KEY (treinid) REFERENCES somda_trein (treinid)
        ');
        $this->addSql('CREATE INDEX IDX_95ACCAE69CBF59B5 ON somda_tdr_trein_treinnummerlijst (treinnummerlijst_id)');
        $this->addSql('CREATE INDEX IDX_95ACCAE668F454BD ON somda_tdr_trein_treinnummerlijst (treinid)');

        $this->addSql('
            ALTER TABLE somda_users_onlineid
            CHANGE uid uid BIGINT DEFAULT NULL,
            CHANGE expire_datetime expire_datetime BIGINT NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_users_onlineid ADD CONSTRAINT FK_8393E3DC539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_8393E3DC539B0606 ON somda_users_onlineid (uid)');

        $this->addSql('DROP INDEX UNIQ_D79EBDD6A0D96FBF ON somda_users');
        $this->addSql('DROP INDEX UNIQ_D79EBDD692FC23A8 ON somda_users');
        $this->addSql('DROP INDEX UNIQ_D79EBDD6C05FB297 ON somda_users');
        $this->addSql('
            ALTER TABLE somda_users
            ADD active BIGINT NOT NULL,
            DROP username_canonical,
            DROP email_canonical,
            DROP enabled,
            DROP salt,
            DROP last_login,
            DROP confirmation_token,
            DROP password_requested_at,
            CHANGE spots_ok spots_ok INT(11) NOT NULL DEFAULT 0,
            CHANGE username username VARCHAR(10) NOT NULL,
            CHANGE password password VARCHAR(32) NOT NULL,
            CHANGE email email VARCHAR(60) NOT NULL,
            CHANGE cookie_ok cookie_ok VARCHAR(3) NOT NULL,
            CHANGE actkey actkey VARCHAR(32) NOT NULL
        ');

        $this->addSql('ALTER TABLE somda_help_text CHANGE google_channel google_channel VARCHAR(10) NOT NULL');
        $this->addSql('
            ALTER TABLE somda_sns_spoor_nieuws
            CHANGE sns_actief sns_actief TINYINT(1) DEFAULT \'1\' NOT NULL,
            CHANGE sns_gekeurd sns_gekeurd TINYINT(1) NOT NULL,
            CHANGE sns_bijwerken_ok sns_bijwerken_ok TINYINT(1) DEFAULT \'1\' NOT NULL
        ');

        $this->addSql('ALTER TABLE somda_stats_blokken DROP PRIMARY KEY');
        $this->addSql('
            ALTER TABLE somda_stats_blokken
            CHANGE pageviews pageviews BIGINT NOT NULL,
            CHANGE stat_date date DATE NOT NULL
        ');
        $this->addSql('CREATE INDEX IDX_7FAF7B1A711B2385 ON somda_stats_blokken (blokid)');
        $this->addSql('ALTER TABLE somda_stats_blokken ADD PRIMARY KEY (blokid, `date`)');

        $this->addSql('
            ALTER TABLE somda_users_prefs ADD CONSTRAINT FK_3920F080539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('
            ALTER TABLE somda_users_prefs ADD CONSTRAINT FK_3920F08087B0DAC1
            FOREIGN KEY (prefid) REFERENCES somda_prefs (prefid)
        ');
        $this->addSql('CREATE INDEX IDX_3920F080539B0606 ON somda_users_prefs (uid)');
        $this->addSql('CREATE INDEX IDX_3920F08087B0DAC1 ON somda_users_prefs (prefid)');

        $this->addSql('
             ALTER TABLE somda_users_info
             DROP icq,
             CHANGE avatar avatar VARCHAR(30) DEFAULT \'_blank.gif\' NOT NULL,
             CHANGE geslacht geslacht SMALLINT(4) DEFAULT 0 NOT NULL
         ');
        $this->addSql('
            ALTER TABLE somda_users_info ADD CONSTRAINT FK_46F59BD0539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('
            ALTER TABLE somda_users_info ADD CONSTRAINT FK_46F59BD0740E9210
            FOREIGN KEY (bedrijf_id) REFERENCES somda_users_companies (bedrijf_id)
        ');
        $this->addSql('CREATE INDEX IDX_46F59BD0740E9210 ON somda_users_info (bedrijf_id)');

        $this->addSql('
            ALTER TABLE somda_forum_alerts_notes
            CHANGE alertid alertid BIGINT DEFAULT NULL,
            CHANGE authorid authorid BIGINT DEFAULT NULL,
            CHANGE sent_to_reporter sent_to_reporter TINYINT(1) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_forum_alerts_notes ADD CONSTRAINT FK_502511CEF2677207
            FOREIGN KEY (alertid) REFERENCES somda_forum_alerts (id)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_alerts_notes ADD CONSTRAINT FK_502511CE3412DD5F
            FOREIGN KEY (authorid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_502511CE3412DD5F ON somda_forum_alerts_notes (authorid)');

        $this->addSql('
            ALTER TABLE somda_banner
            CHANGE link link VARCHAR(100) NOT NULL,
            CHANGE email email VARCHAR(50) NOT NULL,
            CHANGE views views BIGINT NOT NULL,
            CHANGE hits hits BIGINT NOT NULL,
            CHANGE active active TINYINT(1) DEFAULT 0 NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_banner ADD CONSTRAINT FK_D93888C264FBF307
            FOREIGN KEY (customerid) REFERENCES somda_banner_customer (id)
        ');
        $this->addSql('CREATE INDEX IDX_D93888C264FBF307 ON somda_banner (customerid)');

        $this->addSql('
            ALTER TABLE somda_forum_posts_text ADD CONSTRAINT FK_25A0B80F7510F6AF
            FOREIGN KEY (postid) REFERENCES somda_forum_posts (postid)
        ');

        $this->addSql('ALTER TABLE somda_forum_zoeken_lijst DROP PRIMARY KEY');
        $this->addSql('
            ALTER TABLE somda_forum_zoeken_lijst
            CHANGE titel titel TINYINT(1) NOT NULL,
            CHANGE post_id postid BIGINT NOT NULL
        ');
        $this->addSql('
            DELETE FROM somda_forum_zoeken_lijst
            WHERE woord_id NOT IN (SELECT woord_id FROM somda_forum_zoeken_woorden)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_zoeken_lijst ADD CONSTRAINT FK_C9D9A41EE9BD09BA
            FOREIGN KEY (woord_id) REFERENCES somda_forum_zoeken_woorden (woord_id)
        ');
        $this->addSql(
            'DELETE FROM somda_forum_zoeken_lijst WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)'
        );
        $this->addSql('
            ALTER TABLE somda_forum_zoeken_lijst ADD CONSTRAINT FK_C9D9A41E7510F6AF
            FOREIGN KEY (postid) REFERENCES somda_forum_posts (postid)
        ');
        $this->addSql('CREATE INDEX IDX_C9D9A41EE9BD09BA ON somda_forum_zoeken_lijst (woord_id)');
        $this->addSql('CREATE INDEX IDX_C9D9A41E7510F6AF ON somda_forum_zoeken_lijst (postid)');
        $this->addSql('ALTER TABLE somda_forum_zoeken_lijst ADD PRIMARY KEY (woord_id, postid)');

        $this->addSql('
            ALTER TABLE somda_forum_discussion
            CHANGE forumid forumid BIGINT DEFAULT NULL,
            CHANGE disc_type disc_type BIGINT NOT NULL,
            CHANGE title title VARCHAR(50) NOT NULL,
            CHANGE authorid authorid BIGINT DEFAULT NULL,
            CHANGE viewed viewed BIGINT NOT NULL,
            CHANGE locked locked TINYINT(1) DEFAULT 0 NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_forum_discussion ADD CONSTRAINT FK_64C2DF7EEDB4D5F3
            FOREIGN KEY (forumid) REFERENCES somda_forum_forums (forumid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_discussion ADD CONSTRAINT FK_64C2DF7E3412DD5F
            FOREIGN KEY (authorid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_64C2DF7E3412DD5F ON somda_forum_discussion (authorid)');

        $this->addSql('
            ALTER TABLE somda_logging
            CHANGE uid uid BIGINT DEFAULT NULL,
            CHANGE blokid blokid BIGINT DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_logging ADD CONSTRAINT FK_8127138D539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_8127138D539B0606 ON somda_logging (uid)');
        $this->addSql('CREATE INDEX IDX_8127138D711B2385 ON somda_logging (blokid)');

        $this->addSql('
            ALTER TABLE somda_stats
            CHANGE datum datum DATE NOT NULL,
            CHANGE uniek uniek BIGINT NOT NULL,
            CHANGE pageviews pageviews BIGINT NOT NULL,
            CHANGE pageviews_home pageviews_home BIGINT NOT NULL,
            CHANGE pageviews_func pageviews_func BIGINT NOT NULL,
            CHANGE spots spots BIGINT NOT NULL,
            CHANGE posts posts BIGINT NOT NULL
        ');

        $this->addSql('
            ALTER TABLE somda_poll
            CHANGE question question VARCHAR(200) NOT NULL,
            CHANGE opt_a opt_a VARCHAR(150) NOT NULL,
            CHANGE opt_b opt_b VARCHAR(150) NOT NULL,
            CHANGE opt_c opt_c VARCHAR(150) NOT NULL,
            CHANGE opt_d opt_d VARCHAR(150) NOT NULL
        ');

        $this->addSql('
            ALTER TABLE somda_users_lastvisit
            CHANGE username username VARCHAR(10) NOT NULL,
            CHANGE real_name real_name VARCHAR(40) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_users_lastvisit ADD CONSTRAINT FK_7AA6BFAF539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');

        $this->addSql('ALTER TABLE somda_spot_provincie CHANGE naam naam VARCHAR(15) NOT NULL');

        $this->addSql('
            ALTER TABLE somda_spot_punt_text ADD CONSTRAINT FK_91652C16FD33F6CE
            FOREIGN KEY (puntid) REFERENCES somda_spot_punt (puntid)
        ');
        $this->addSql('
            ALTER TABLE somda_mobiel_logging ADD CONSTRAINT FK_B52A8617539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');

        $this->addSql('CREATE INDEX IDX_B52A8617539B0606 ON somda_mobiel_logging (uid)');

        $this->addSql('
            ALTER TABLE somda_mat_changes ADD CONSTRAINT FK_C6C1DF0D4CD774E2
            FOREIGN KEY (matsmsid) REFERENCES somda_mat_sms (matsmsid)
        ');
        $this->addSql('
            ALTER TABLE somda_mat_changes ADD CONSTRAINT FK_C6C1DF0D539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_C6C1DF0D4CD774E2 ON somda_mat_changes (matsmsid)');
        $this->addSql('CREATE INDEX IDX_C6C1DF0D539B0606 ON somda_mat_changes (uid)');

        $this->addSql('ALTER TABLE somda_forum_discussion_wiki CHANGE discussionid discussionid BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE somda_forum_discussion_wiki ADD CONSTRAINT FK_D487B7F1FCC0F19E
            FOREIGN KEY (discussionid) REFERENCES somda_forum_discussion (discussionid)
        ');

        $this->addSql('
            ALTER TABLE somda_don_donatie
            CHANGE don_uid don_uid BIGINT DEFAULT NULL,
            CHANGE don_ok don_ok TINYINT(1) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_don_donatie ADD CONSTRAINT FK_DE3B771128103CB
            FOREIGN KEY (don_uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_DE3B771128103CB ON somda_don_donatie (don_uid)');

        $this->addSql('
            ALTER TABLE somda_spots
            CHANGE treinid treinid BIGINT DEFAULT NULL,
            CHANGE posid posid BIGINT DEFAULT NULL,
            CHANGE locatieid locatieid BIGINT DEFAULT NULL,
            CHANGE matid matid BIGINT DEFAULT NULL,
            CHANGE uid uid BIGINT DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_spots ADD CONSTRAINT FK_11A6C5C8BC0CC550
            FOREIGN KEY (in_spotid) REFERENCES somda_in_spots (spotid)
        ');
        $this->addSql('
            ALTER TABLE somda_spots ADD CONSTRAINT FK_11A6C5C8890261A4
            FOREIGN KEY (matid) REFERENCES somda_mat (matid)
        ');
        $this->addSql('
            ALTER TABLE somda_spots ADD CONSTRAINT FK_11A6C5C868F454BD
            FOREIGN KEY (treinid) REFERENCES somda_trein (treinid)
        ');
        $this->addSql('UPDATE `somda_spots` SET posid = 1 WHERE posid NOT IN (SELECT posid FROM somda_positie)');
        $this->addSql('
            ALTER TABLE somda_spots ADD CONSTRAINT FK_11A6C5C8F4E25321
            FOREIGN KEY (posid) REFERENCES somda_positie (posid)
        ');
        $this->addSql('
            ALTER TABLE somda_spots ADD CONSTRAINT FK_11A6C5C8D6E3DC6C
            FOREIGN KEY (locatieid) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('
            ALTER TABLE somda_spots ADD CONSTRAINT FK_11A6C5C8539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_11A6C5C8BC0CC550 ON somda_spots (in_spotid)');
        $this->addSql('CREATE INDEX IDX_11A6C5C868F454BD ON somda_spots (treinid)');
        $this->addSql('CREATE INDEX IDX_11A6C5C8F4E25321 ON somda_spots (posid)');
        $this->addSql('CREATE INDEX IDX_11A6C5C8D6E3DC6C ON somda_spots (locatieid)');

        $this->addSql('
            ALTER TABLE somda_spots_extra
            CHANGE extra extra VARCHAR(255) NOT NULL,
            CHANGE user_extra user_extra VARCHAR(255) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_spots_extra ADD CONSTRAINT FK_6EAD9515BFB6C75
            FOREIGN KEY (spotid) REFERENCES somda_spots (spotid)
        ');

        $this->addSql('
            ALTER TABLE somda_rijdagen
            DROP rijdagen,
            CHANGE ma ma BIGINT DEFAULT NULL,
            CHANGE di di BIGINT DEFAULT NULL,
            CHANGE wo wo BIGINT DEFAULT NULL,
            CHANGE do do BIGINT DEFAULT NULL,
            CHANGE vr vr BIGINT DEFAULT NULL,
            CHANGE za za BIGINT DEFAULT NULL,
            CHANGE zf zf BIGINT DEFAULT NULL
        ');

        $this->addSql('
            ALTER TABLE somda_banner_customer_user
            CHANGE allowed_new allowed_new TINYINT(1) DEFAULT 0 NOT NULL,
            CHANGE allowed_max_views allowed_max_views TINYINT(1) DEFAULT 0 NOT NULL,
            CHANGE allowed_max_hits allowed_max_hits TINYINT(1) DEFAULT 0 NOT NULL,
            CHANGE allowed_max_date allowed_max_date TINYINT(1) DEFAULT 0 NOT NULL,
            CHANGE allowed_deactivate allowed_deactivate TINYINT(1) DEFAULT 0 NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_banner_customer_user ADD CONSTRAINT FK_C9A88E10BF396750
            FOREIGN KEY (id) REFERENCES somda_banner_customer (id)
        ');
        $this->addSql('
            ALTER TABLE somda_banner_customer_user ADD CONSTRAINT FK_C9A88E10539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_C9A88E10BF396750 ON somda_banner_customer_user (id)');
        $this->addSql('CREATE INDEX IDX_C9A88E10539B0606 ON somda_banner_customer_user (uid)');

        $this->addSql('
            ALTER TABLE somda_in_spots
            DROP dagenverschil,
            DROP dgrid,
            DROP dgrnr,
            DROP dgrid_nu,
            DROP dgrnr_nu,
            DROP mat_datum,
            DROP mat_tijd,
            DROP mat_dgrid,
            DROP mat_dgrnr,
            DROP dienst_datum,
            DROP dienst_tijd,
            DROP dienst_matid,
            CHANGE extra extra VARCHAR(255) NOT NULL,
            CHANGE uid uid BIGINT DEFAULT NULL,
            CHANGE locatie locatie VARCHAR(15) NOT NULL,
            CHANGE mat mat VARCHAR(20) NOT NULL,
            CHANGE treinnr treinnr VARCHAR(15) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_in_spots ADD CONSTRAINT FK_56649567890261A4
            FOREIGN KEY (matid) REFERENCES somda_mat (matid)
        ');
        $this->addSql('
            ALTER TABLE somda_in_spots ADD CONSTRAINT FK_5664956768F454BD
            FOREIGN KEY (treinid) REFERENCES somda_trein (treinid)
        ');
        $this->addSql('
            ALTER TABLE somda_in_spots ADD CONSTRAINT FK_56649567F4E25321
            FOREIGN KEY (posid) REFERENCES somda_positie (posid)
        ');
        $this->addSql('
            ALTER TABLE somda_in_spots ADD CONSTRAINT FK_56649567D6E3DC6C
            FOREIGN KEY (locatieid) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('
            ALTER TABLE somda_in_spots ADD CONSTRAINT FK_56649567539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('
            ALTER TABLE somda_in_spots ADD CONSTRAINT FK_566495672065B5FE
            FOREIGN KEY (spotstabel_id) REFERENCES somda_spots (spotid)
        ');
        $this->addSql('CREATE INDEX IDX_56649567890261A4 ON somda_in_spots (matid)');
        $this->addSql('CREATE INDEX IDX_5664956768F454BD ON somda_in_spots (treinid)');
        $this->addSql('CREATE INDEX IDX_56649567F4E25321 ON somda_in_spots (posid)');
        $this->addSql('CREATE INDEX IDX_56649567D6E3DC6C ON somda_in_spots (locatieid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_566495672065B5FE ON somda_in_spots (spotstabel_id)');

        $this->addSql('
            ALTER TABLE somda_forum_alerts
            CHANGE postid postid BIGINT DEFAULT NULL,
            CHANGE senderid senderid BIGINT DEFAULT NULL,
            CHANGE closed closed TINYINT(1) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_forum_alerts ADD CONSTRAINT FK_A2F3B42C7510F6AF
            FOREIGN KEY (postid) REFERENCES somda_forum_posts (postid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_alerts ADD CONSTRAINT FK_A2F3B42C65525B5F
            FOREIGN KEY (senderid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_A2F3B42C65525B5F ON somda_forum_alerts (senderid)');

        $this->addSql('ALTER TABLE somda_ddar CHANGE extra extra VARCHAR(150) NOT NULL');

        $this->addSql('ALTER TABLE somda_ipb_ip_bans CHANGE ipb_uid ipb_uid BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE somda_ipb_ip_bans ADD CONSTRAINT FK_99E5BE3C54263F44
            FOREIGN KEY (ipb_uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_99E5BE3C54263F44 ON somda_ipb_ip_bans (ipb_uid)');

        $this->addSql('ALTER TABLE somda_verk_cats CHANGE name name VARCHAR(20) NOT NULL');
        $this->addSql('
            ALTER TABLE somda_spot_punt
            CHANGE provincieid provincieid BIGINT DEFAULT NULL,
            CHANGE naam naam VARCHAR(50) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_spot_punt ADD CONSTRAINT FK_6164DEDFA5300E5
            FOREIGN KEY (afkid_locatie) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('
            ALTER TABLE somda_spot_punt ADD CONSTRAINT FK_6164DED2C0A1CCC
            FOREIGN KEY (afkid_traject_1) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('
            ALTER TABLE somda_spot_punt ADD CONSTRAINT FK_6164DEDB5034D76
            FOREIGN KEY (afkid_traject_2) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('
            ALTER TABLE somda_spot_punt ADD CONSTRAINT FK_6164DEDE2D85197
            FOREIGN KEY (afkid_dks) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('
            ALTER TABLE somda_spot_punt ADD CONSTRAINT FK_6164DED8A6CCEB4
            FOREIGN KEY (provincieid) REFERENCES somda_spot_provincie (provincieid)
        ');
        $this->addSql('CREATE INDEX IDX_6164DEDFA5300E5 ON somda_spot_punt (afkid_locatie)');
        $this->addSql('CREATE INDEX IDX_6164DED2C0A1CCC ON somda_spot_punt (afkid_traject_1)');
        $this->addSql('CREATE INDEX IDX_6164DEDB5034D76 ON somda_spot_punt (afkid_traject_2)');
        $this->addSql('CREATE INDEX IDX_6164DEDE2D85197 ON somda_spot_punt (afkid_dks)');
        $this->addSql('CREATE INDEX IDX_6164DED8A6CCEB4 ON somda_spot_punt (provincieid)');

        $this->addSql('
            ALTER TABLE somda_jargon
            CHANGE term term VARCHAR(15) NOT NULL,
            CHANGE image image VARCHAR(20) NOT NULL,
            CHANGE description description VARCHAR(150) NOT NULL
        ');

        $this->addSql('ALTER TABLE somda_prefs CHANGE type type VARCHAR(50) NOT NULL');

        $this->addSql('DROP INDEX UNIQ_46C809555E237E06 ON somda_groups');
        $this->addSql('ALTER TABLE somda_groups CHANGE name name VARCHAR(15) NOT NULL');

        $this->addSql('ALTER TABLE somda_users_groups DROP PRIMARY KEY');
        $this->addSql('
            ALTER TABLE somda_users_groups ADD CONSTRAINT FK_B2ACF0767805AC12
            FOREIGN KEY (groupid) REFERENCES somda_groups (groupid)
        ');
        $this->addSql('
            ALTER TABLE somda_users_groups ADD CONSTRAINT FK_B2ACF076539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_B2ACF0767805AC12 ON somda_users_groups (groupid)');
        $this->addSql('CREATE INDEX IDX_B2ACF076539B0606 ON somda_users_groups (uid)');
        $this->addSql('ALTER TABLE somda_users_groups ADD PRIMARY KEY (groupid, uid)');

        $this->addSql('
             ALTER TABLE somda_poll_votes ADD CONSTRAINT FK_75CFE3876F5F43AE
             FOREIGN KEY (pollid) REFERENCES somda_poll (pollid)
         ');
        $this->addSql('
            ALTER TABLE somda_poll_votes ADD CONSTRAINT FK_75CFE387539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_75CFE3876F5F43AE ON somda_poll_votes (pollid)');
        $this->addSql('CREATE INDEX IDX_75CFE387539B0606 ON somda_poll_votes (uid)');

        $this->addSql('
             ALTER TABLE somda_news
             CHANGE title title VARCHAR(50) NOT NULL,
             CHANGE archief archief TINYINT(1) NOT NULL,
             CHANGE news_timestamp timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
         ');

        $this->addSql('ALTER TABLE somda_news_read DROP PRIMARY KEY');
        $this->addSql('
            ALTER TABLE somda_news_read ADD CONSTRAINT FK_AF652C58C510C37
            FOREIGN KEY (newsid) REFERENCES somda_news (newsid)
        ');
        $this->addSql('
            ALTER TABLE somda_news_read ADD CONSTRAINT FK_AF652C5539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_AF652C58C510C37 ON somda_news_read (newsid)');
        $this->addSql('CREATE INDEX IDX_AF652C5539B0606 ON somda_news_read (uid)');
        $this->addSql('ALTER TABLE somda_news_read ADD PRIMARY KEY (newsid, uid)');

        $this->addSql('
             ALTER TABLE somda_drgl_logging
             CHANGE drglid drglid BIGINT DEFAULT NULL,
             CHANGE uid uid BIGINT DEFAULT NULL
         ');
        $this->addSql('
            ALTER TABLE somda_drgl_logging ADD CONSTRAINT FK_E97A0CEBB869D711
            FOREIGN KEY (drglid) REFERENCES somda_drgl (drglid)
        ');
        $this->addSql('
            ALTER TABLE somda_drgl_logging ADD CONSTRAINT FK_E97A0CEB539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_E97A0CEBB869D711 ON somda_drgl_logging (drglid)');
        $this->addSql('CREATE INDEX IDX_E97A0CEB539B0606 ON somda_drgl_logging (uid)');

        $this->addSql('
            ALTER TABLE somda_mat_sms
            CHANGE typeid typeid BIGINT DEFAULT NULL,
            CHANGE extra extra VARCHAR(255) NOT NULL,
            CHANGE index_regel index_regel TINYINT(1) NOT NULL
        ');

        $this->addSql('ALTER TABLE somda_tdr_s_e DROP PRIMARY KEY');
        $this->addSql('
            ALTER TABLE somda_tdr_s_e
            CHANGE tdr_nr tdr_nr BIGINT NOT NULL,
            CHANGE v_locatieid v_locatieid BIGINT DEFAULT NULL,
            CHANGE a_locatieid a_locatieid BIGINT DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_s_e ADD CONSTRAINT FK_1BACB963AE60685A
            FOREIGN KEY (tdr_nr) REFERENCES somda_tdr_drgl (tdr_nr)
        ');
        $this->addSql('DELETE FROM somda_tdr_s_e WHERE treinid NOT IN (SELECT treinid FROM somda_trein)');
        $this->addSql('
            ALTER TABLE somda_tdr_s_e ADD CONSTRAINT FK_1BACB96368F454BD
            FOREIGN KEY (treinid) REFERENCES somda_trein (treinid)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_s_e ADD CONSTRAINT FK_1BACB9635E53C5B
            FOREIGN KEY (v_locatieid) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_s_e ADD CONSTRAINT FK_1BACB9638228ED13
            FOREIGN KEY (a_locatieid) REFERENCES somda_verk (afkid)
        ');
        $this->addSql('CREATE INDEX IDX_1BACB963AE60685A ON somda_tdr_s_e (tdr_nr)');
        $this->addSql('CREATE INDEX IDX_1BACB96368F454BD ON somda_tdr_s_e (treinid)');
        $this->addSql('CREATE INDEX IDX_1BACB9635E53C5B ON somda_tdr_s_e (v_locatieid)');
        $this->addSql('CREATE INDEX IDX_1BACB9638228ED13 ON somda_tdr_s_e (a_locatieid)');
        $this->addSql('ALTER TABLE somda_tdr_s_e ADD PRIMARY KEY (tdr_nr, treinid, dag)');

        $this->addSql('ALTER TABLE somda_blokken DROP FOREIGN KEY FRK_parent_block');
        $this->addSql('
            ALTER TABLE somda_blokken
            DROP `type`,
            CHANGE blokid blokid BIGINT AUTO_INCREMENT NOT NULL,
            CHANGE do_separator do_separator TINYINT(1) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_blokken ADD CONSTRAINT FK_B4865B064F2A0381
            FOREIGN KEY (parent_block) REFERENCES somda_blokken (blokid)
        ');

        $this->addSql('
            ALTER TABLE somda_help_text ADD CONSTRAINT FK_397D7775711B2385
            FOREIGN KEY (blokid) REFERENCES somda_blokken (blokid)
        ');

        $this->addSql('
            ALTER TABLE somda_stats_blokken ADD CONSTRAINT FK_7FAF7B1A711B2385
            FOREIGN KEY (blokid) REFERENCES somda_blokken (blokid)
        ');

        $this->addSql('
            ALTER TABLE somda_logging ADD CONSTRAINT FK_8127138D711B2385
            FOREIGN KEY (blokid) REFERENCES somda_blokken (blokid)
        ');

        $this->addSql('
             ALTER TABLE somda_mat_types
             CHANGE typeid typeid BIGINT AUTO_INCREMENT NOT NULL,
             CHANGE omschrijving omschrijving VARCHAR(25) NOT NULL
         ');

        $this->addSql('
            ALTER TABLE somda_mat_sms ADD CONSTRAINT FK_2FC3E54DE70B032
            FOREIGN KEY (typeid) REFERENCES somda_mat_types (typeid)
        ');

        $this->addSql('ALTER TABLE somda_trein CHANGE treinnr treinnr VARCHAR(15) NOT NULL');

        $this->addSql('ALTER TABLE somda_sht_shout CHANGE sht_uid sht_uid BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE somda_sht_shout ADD CONSTRAINT FK_88E10AFB97AD1E10
            FOREIGN KEY (sht_uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_88E10AFB97AD1E10 ON somda_sht_shout (sht_uid)');

        $this->addSql('
            ALTER TABLE somda_forum_posts
            CHANGE authorid authorid BIGINT DEFAULT NULL,
            CHANGE discussionid discussionid BIGINT DEFAULT NULL,
            CHANGE wiki_check wiki_check TINYINT(1) NOT NULL,
            CHANGE sign_on sign_on TINYINT(1) NOT NULL,
            CHANGE `timestamp` `timestamp` DATE NOT NULL,
            CHANGE edit_timestamp edit_timestamp DATE DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_forum_posts ADD CONSTRAINT FK_40FD24D3412DD5F
            FOREIGN KEY (authorid) REFERENCES somda_users (uid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_posts ADD CONSTRAINT FK_40FD24DFCC0F19E
            FOREIGN KEY (discussionid) REFERENCES somda_forum_discussion (discussionid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_posts ADD CONSTRAINT FK_40FD24D9ECDC13D
            FOREIGN KEY (edit_uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_posts ADD CONSTRAINT FK_40FD24D143C6493
            FOREIGN KEY (wiki_uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_40FD24D9ECDC13D ON somda_forum_posts (edit_uid)');
        $this->addSql('CREATE INDEX IDX_40FD24D143C6493 ON somda_forum_posts (wiki_uid)');
        $this->addSql('CREATE INDEX idx_47961_timestamp ON somda_forum_posts (timestamp)');

        $this->addSql('ALTER TABLE somda_banner_views CHANGE bannerid bannerid BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE somda_banner_views ADD CONSTRAINT FK_F1B9EA066BBC5658
            FOREIGN KEY (bannerid) REFERENCES somda_banner (bannerid)
        ');

        $this->addSql('
             ALTER TABLE somda_forum_forums
             CHANGE catid catid BIGINT DEFAULT NULL,
             CHANGE `name` `name` VARCHAR(40) NOT NULL,
             CHANGE description description VARCHAR(100) NOT NULL
         ');
        $this->addSql('
            ALTER TABLE somda_forum_forums ADD CONSTRAINT FK_ABD72EFF3632DFC5
            FOREIGN KEY (catid) REFERENCES somda_forum_cats (catid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_mods ADD CONSTRAINT FK_E20AB6A4EDB4D5F3
            FOREIGN KEY (forumid) REFERENCES somda_forum_forums (forumid)
        ');
        $this->addSql('
            ALTER TABLE somda_forum_mods ADD CONSTRAINT FK_E20AB6A4539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_E20AB6A4EDB4D5F3 ON somda_forum_mods (forumid)');
        $this->addSql('CREATE INDEX IDX_E20AB6A4539B0606 ON somda_forum_mods (uid)');

        $this->addSql('
            ALTER TABLE somda_drgl
            CHANGE title title VARCHAR(75) NOT NULL,
            CHANGE image image VARCHAR(20) NOT NULL,
            CHANGE werkzaamheden werkzaamheden TINYINT(1) NOT NULL,
            CHANGE `public` `public` TINYINT(1) NOT NULL
        ');

        $this->addSql('
             ALTER TABLE somda_drgl_read ADD CONSTRAINT FK_7CF8CCE9B869D711
             FOREIGN KEY (drglid) REFERENCES somda_drgl (drglid)
         ');
        $this->addSql('
            ALTER TABLE somda_drgl_read ADD CONSTRAINT FK_7CF8CCE9539B0606
            FOREIGN KEY (uid) REFERENCES somda_users (uid)
        ');
        $this->addSql('CREATE INDEX IDX_7CF8CCE9B869D711 ON somda_drgl_read (drglid)');
        $this->addSql('CREATE INDEX IDX_7CF8CCE9539B0606 ON somda_drgl_read (uid)');

        $this->addSql('ALTER TABLE somda_tdr_trein_mat DROP PRIMARY KEY');
        $this->addSql('
            ALTER TABLE somda_tdr_trein_mat
            CHANGE tdr_nr tdr_nr BIGINT NOT NULL,
            CHANGE mat_type_id mat_type_id BIGINT DEFAULT NULL,
            CHANGE spots spots BIGINT NOT NULL
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_trein_mat ADD CONSTRAINT FK_C2BF79AAAE60685A
            FOREIGN KEY (tdr_nr) REFERENCES somda_tdr_drgl (tdr_nr)
        ');
        $this->addSql('DELETE FROM somda_tdr_trein_mat WHERE treinid NOT IN (SELECT treinid FROM somda_trein)');
        $this->addSql('
            ALTER TABLE somda_tdr_trein_mat ADD CONSTRAINT FK_C2BF79AA68F454BD
            FOREIGN KEY (treinid) REFERENCES somda_trein (treinid)
        ');
        $this->addSql('UPDATE somda_tdr_trein_mat SET posid = 1 WHERE posid NOT IN (SELECT posid FROM somda_positie)');
        $this->addSql('
            ALTER TABLE somda_tdr_trein_mat ADD CONSTRAINT FK_C2BF79AAF4E25321
            FOREIGN KEY (posid) REFERENCES somda_positie (posid)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_trein_mat ADD CONSTRAINT FK_C2BF79AA394F068
            FOREIGN KEY (mat_naam_id) REFERENCES somda_mat_naam (id)
        ');
        $this->addSql(
            'DELETE FROM somda_tdr_trein_mat WHERE mat_type_id NOT IN (SELECT id FROM somda_mat_type_patterns)'
        );
        $this->addSql('
            ALTER TABLE somda_tdr_trein_mat ADD CONSTRAINT FK_C2BF79AAB78B25C3
            FOREIGN KEY (mat_type_id) REFERENCES somda_mat_type_patterns (id)
        ');
        $this->addSql('CREATE INDEX IDX_C2BF79AAAE60685A ON somda_tdr_trein_mat (tdr_nr)');
        $this->addSql('CREATE INDEX IDX_C2BF79AA68F454BD ON somda_tdr_trein_mat (treinid)');
        $this->addSql('CREATE INDEX IDX_C2BF79AAF4E25321 ON somda_tdr_trein_mat (posid)');
        $this->addSql('CREATE INDEX IDX_C2BF79AA394F068 ON somda_tdr_trein_mat (mat_naam_id)');
        $this->addSql('CREATE INDEX IDX_C2BF79AAB78B25C3 ON somda_tdr_trein_mat (mat_type_id)');
        $this->addSql('ALTER TABLE somda_tdr_trein_mat ADD PRIMARY KEY (tdr_nr, treinid, posid, dag)');

        for ($table = 0; $table <= 9; ++$table) {
            $this->addSql('ALTER TABLE somda_forum_read_' . $table . ' DROP PRIMARY KEY');
            $this->addSql(
                'DELETE FROM somda_forum_read_' . $table . ' WHERE postid NOT IN (SELECT postid FROM somda_forum_posts)'
            );
            $this->addSql('
                ALTER TABLE somda_forum_read_' . $table . ' ADD CONSTRAINT FK_forum_read_post_' . $table . '
                FOREIGN KEY (postid) REFERENCES somda_forum_posts (postid)
            ');
            $this->addSql('DELETE FROM somda_forum_read_' . $table . ' WHERE uid NOT IN (SELECT uid FROM somda_users)');
            $this->addSql('
                ALTER TABLE somda_forum_read_' . $table . ' ADD CONSTRAINT FK_forum_read_user_' . $table . '
                FOREIGN KEY (uid) REFERENCES somda_users (uid)
            ');
            $this->addSql('CREATE INDEX IDX_forum_read_' . $table . ' ON somda_forum_read_' . $table . ' (postid)');
            $this->addSql('ALTER TABLE somda_forum_read_' . $table . ' ADD PRIMARY KEY (postid, uid)');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
