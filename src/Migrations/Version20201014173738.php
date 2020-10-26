<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201014173738 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Migrate table-names to correct structure - all tables except forum-read';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            ALTER TABLE bac_banner_customer MODIFY id BIGINT NOT NULL;
            ALTER TABLE bac_banner_customer DROP PRIMARY KEY;
            ALTER TABLE bac_banner_customer
                CHANGE max_views bac_max_views INT DEFAULT NULL,
                CHANGE max_hits bac_max_hits INT DEFAULT NULL,
                CHANGE max_days bac_max_days INT DEFAULT NULL,
                CHANGE id bac_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE name bac_name VARCHAR(6) NOT NULL;
            ALTER TABLE bac_banner_customer ADD PRIMARY KEY (bac_id);
        
            ALTER TABLE bah_banner_hit MODIFY id BIGINT NOT NULL;
            ALTER TABLE bah_banner_hit DROP PRIMARY KEY;
            ALTER TABLE bah_banner_hit
                CHANGE id bah_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE bannerid bah_ban_id BIGINT DEFAULT NULL,
                CHANGE timestamp bah_timestamp DATETIME NOT NULL,
                CHANGE ip_address bah_ip_address BIGINT NOT NULL;
            ALTER TABLE bah_banner_hit ADD CONSTRAINT FRK_bah_ban_id
                FOREIGN KEY (bah_ban_id) REFERENCES ban_banner (ban_id);
            CREATE INDEX IDX_C7410BF68846732 ON bah_banner_hit (bah_ban_id);
            ALTER TABLE bah_banner_hit ADD PRIMARY KEY (bah_id);
            
            ALTER TABLE new_news MODIFY newsid BIGINT NOT NULL;
            ALTER TABLE new_news DROP PRIMARY KEY;
            ALTER TABLE new_news
                CHANGE newsid new_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE timestamp new_timestamp DATETIME NOT NULL,
                CHANGE title new_title VARCHAR(50) NOT NULL,
                CHANGE text new_text LONGTEXT NOT NULL,
                CHANGE archief new_archived TINYINT(1) NOT NULL;
            ALTER TABLE new_news ADD PRIMARY KEY (new_id);

            ALTER TABLE ner_news_read DROP PRIMARY KEY;
            ALTER TABLE ner_news_read CHANGE newsid ner_new_id BIGINT NOT NULL, CHANGE uid ner_use_id BIGINT NOT NULL;
            ALTER TABLE ner_news_read ADD CONSTRAINT FRK_ner_new_id
                FOREIGN KEY (ner_new_id) REFERENCES new_news (new_id);
            ALTER TABLE ner_news_read ADD CONSTRAINT FRK_ner_use_id
                FOREIGN KEY (ner_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_E421EC1D7C85FD ON ner_news_read (ner_new_id);
            CREATE INDEX IDX_E421EC171DD8A93 ON ner_news_read (ner_use_id);
            ALTER TABLE ner_news_read ADD PRIMARY KEY (ner_new_id, ner_use_id);

            ALTER TABLE fpn_forum_post_alert_note MODIFY id BIGINT NOT NULL;
            ALTER TABLE fpn_forum_post_alert_note DROP PRIMARY KEY;
            ALTER TABLE fpn_forum_post_alert_note
                CHANGE alertid fpn_fpa_id BIGINT DEFAULT NULL,
                CHANGE authorid fpn_author_use_id BIGINT DEFAULT NULL,
                CHANGE id fpn_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE timestamp fpn_timestamp DATETIME NOT NULL,
                CHANGE sent_to_reporter fpn_sent_to_reporter TINYINT(1) NOT NULL,
                CHANGE text fpn_text LONGTEXT NOT NULL;
            ALTER TABLE fpn_forum_post_alert_note ADD CONSTRAINT FRK_fpn_fpa_id
                FOREIGN KEY (fpn_fpa_id) REFERENCES fpa_forum_post_alert (fpa_id);
            ALTER TABLE fpn_forum_post_alert_note ADD CONSTRAINT FRK_fpn_author_use_id
                FOREIGN KEY (fpn_author_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_fpn_author_use_id ON fpn_forum_post_alert_note (fpn_author_use_id);
            CREATE INDEX IDX_fpn_fpa_id ON fpn_forum_post_alert_note (fpn_fpa_id);
            ALTER TABLE fpn_forum_post_alert_note ADD PRIMARY KEY (fpn_id);

            ALTER TABLE rnf_rail_news_source_feed MODIFY snf_id BIGINT NOT NULL;
            ALTER TABLE rnf_rail_news_source_feed DROP PRIMARY KEY;
            ALTER TABLE rnf_rail_news_source_feed
                CHANGE snf_id rnf_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE snf_snb_id rnf_rns_id BIGINT DEFAULT NULL,
                CHANGE snf_url rnf_url VARCHAR(255) NOT NULL,
                CHANGE snf_filter_results rnf_filter_results TINYINT(1) DEFAULT \'0\' NOT NULL;
            ALTER TABLE rnf_rail_news_source_feed ADD CONSTRAINT FRK_rnf_rns_id
                FOREIGN KEY (rnf_rns_id) REFERENCES rns_rail_news_source (rns_id);
            CREATE INDEX IDX_rnf_rns_id ON rnf_rail_news_source_feed (rnf_rns_id);
            ALTER TABLE rnf_rail_news_source_feed ADD PRIMARY KEY (rnf_id);

            ALTER TABLE ran_rail_news MODIFY sns_id BIGINT NOT NULL;
            ALTER TABLE ran_rail_news DROP PRIMARY KEY;
            ALTER TABLE ran_rail_news
                CHANGE sns_actief ran_active TINYINT(1) DEFAULT \'1\' NOT NULL,
                CHANGE sns_bijwerken_ok ran_automatic_updates TINYINT(1) DEFAULT \'1\' NOT NULL,
                CHANGE sns_id ran_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE sns_snb_id ran_rns_id BIGINT DEFAULT NULL,
                CHANGE sns_titel ran_title VARCHAR(100) NOT NULL,
                CHANGE sns_url ran_url VARCHAR(255) NOT NULL,
                CHANGE sns_introductie ran_introduction LONGTEXT NOT NULL,
                CHANGE sns_timestamp ran_timestamp DATETIME NOT NULL;
            ALTER TABLE ran_rail_news ADD CONSTRAINT FRK_ran_rns_id
                FOREIGN KEY (ran_rns_id) REFERENCES rns_rail_news_source (rns_id);
            CREATE INDEX IDX_ran_rns_id ON ran_rail_news (ran_rns_id);
            ALTER TABLE ran_rail_news ADD PRIMARY KEY (ran_id);

            ALTER TABLE jar_jargon MODIFY jargonid BIGINT NOT NULL;
            ALTER TABLE jar_jargon DROP PRIMARY KEY;
            ALTER TABLE jar_jargon
                CHANGE jargonid jar_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE term jar_term VARCHAR(15) NOT NULL,
                CHANGE image jar_image VARCHAR(20) NOT NULL,
                CHANGE description jar_description VARCHAR(150) NOT NULL;
            ALTER TABLE jar_jargon ADD PRIMARY KEY (jar_id);

            ALTER TABLE pov_poll_vote DROP PRIMARY KEY;
            ALTER TABLE pov_poll_vote
                CHANGE pollid pov_pol_id BIGINT NOT NULL,
                CHANGE uid pov_use_id BIGINT NOT NULL,
                CHANGE vote pov_vote INT DEFAULT 0 NOT NULL;
            ALTER TABLE pov_poll_vote ADD CONSTRAINT FRK_pov_pol_id
                FOREIGN KEY (pov_pol_id) REFERENCES pol_poll (pol_id);
            ALTER TABLE pov_poll_vote ADD CONSTRAINT FRK_pov_use_id
                FOREIGN KEY (pov_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_pov_pol_id ON pov_poll_vote (pov_pol_id);
            CREATE INDEX IDX_pov_use_id ON pov_poll_vote (pov_use_id);
            ALTER TABLE pov_poll_vote ADD PRIMARY KEY (pov_pol_id, pov_use_id);

            ALTER TABLE cha_characteristic MODIFY karakteristiek_id BIGINT NOT NULL;
            ALTER TABLE cha_characteristic DROP PRIMARY KEY;
            ALTER TABLE cha_characteristic
                CHANGE karakteristiek_id cha_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE naam cha_name VARCHAR(5) NOT NULL,
                CHANGE omschrijving cha_description VARCHAR(25) NOT NULL;
            CREATE UNIQUE INDEX UNQ_cha_name ON cha_characteristic (cha_name);
            ALTER TABLE cha_characteristic ADD PRIMARY KEY (cha_id);

            ALTER TABLE fsl_forum_search_list DROP PRIMARY KEY;
            ALTER TABLE fsl_forum_search_list
                CHANGE woord_id fsl_fsw_id BIGINT NOT NULL,
                CHANGE postid fsl_fop_id BIGINT NOT NULL,
                CHANGE titel fsl_in_title TINYINT(1) NOT NULL;
            ALTER TABLE fsl_forum_search_list ADD CONSTRAINT FRK_fsl_fsw_id
                FOREIGN KEY (fsl_fsw_id) REFERENCES fsw_forum_search_word (fsw_id);
            ALTER TABLE fsl_forum_search_list ADD CONSTRAINT FRK_fsl_fop_id
                FOREIGN KEY (fsl_fop_id) REFERENCES fop_forum_post (fop_id);
            CREATE INDEX IDX_fsl_fsw_id ON fsl_forum_search_list (fsl_fsw_id);
            CREATE INDEX IDX_fsl_fop_id ON fsl_forum_search_list (fsl_fop_id);
            ALTER TABLE fsl_forum_search_list ADD PRIMARY KEY (fsl_fsw_id, fsl_fop_id);
            
            ALTER TABLE pot_point_of_interest_text DROP PRIMARY KEY;
            ALTER TABLE pot_point_of_interest_text
                CHANGE route_auto pot_route_car LONGTEXT NOT NULL,
                CHANGE route_ov pot_route_public_transport LONGTEXT NOT NULL,
                CHANGE bijzonderheden pot_particularities LONGTEXT NOT NULL,
                CHANGE puntid pot_poi_id BIGINT NOT NULL;
            ALTER TABLE pot_point_of_interest_text ADD CONSTRAINT FRK_pot_poi_id
                FOREIGN KEY (pot_poi_id) REFERENCES poi_point_of_interest (poi_id);
            ALTER TABLE pot_point_of_interest_text ADD PRIMARY KEY (pot_poi_id);

            ALTER TABLE gro_group MODIFY groupid BIGINT NOT NULL;
            ALTER TABLE gro_group DROP PRIMARY KEY;
            ALTER TABLE gro_group
                CHANGE groupid gro_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE name gro_name VARCHAR(15) NOT NULL,
                CHANGE roles gro_roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\';
            ALTER TABLE gro_group ADD PRIMARY KEY (gro_id);

            ALTER TABLE usg_user_group DROP PRIMARY KEY;
            ALTER TABLE usg_user_group CHANGE groupid usg_gro_id BIGINT NOT NULL, CHANGE uid usg_use_id BIGINT NOT NULL;
            ALTER TABLE usg_user_group ADD CONSTRAINT FRK_usg_gro_id
                FOREIGN KEY (usg_gro_id) REFERENCES gro_group (gro_id);
            ALTER TABLE usg_user_group ADD CONSTRAINT FRK_usg_use_id
                FOREIGN KEY (usg_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_usg_gro_id ON usg_user_group (usg_gro_id);
            CREATE INDEX IDX_usg_use_id ON usg_user_group (usg_use_id);
            ALTER TABLE usg_user_group ADD PRIMARY KEY (usg_gro_id, usg_use_id);

            ALTER TABLE use_user MODIFY uid BIGINT NOT NULL;
            ALTER TABLE use_user DROP PRIMARY KEY;
            ALTER TABLE use_user
                CHANGE ban_expire_timestamp use_ban_expire_timestamp DATETIME DEFAULT NULL,
                CHANGE last_visit use_last_visit DATETIME DEFAULT NULL,
                CHANGE api_token use_api_token VARCHAR(23) DEFAULT NULL,
                CHANGE api_token_expiry_timestamp use_api_token_expiry_timestamp DATETIME DEFAULT NULL,
                CHANGE uid use_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE active use_active TINYINT(1) NOT NULL,
                CHANGE spots_ok use_spots_ok INT NOT NULL,
                CHANGE username use_username VARCHAR(20) NOT NULL,
                CHANGE name use_name VARCHAR(100) DEFAULT NULL,
                CHANGE password use_password VARCHAR(255) NOT NULL,
                CHANGE email use_email VARCHAR(100) NOT NULL,
                CHANGE cookie_ok use_cookie_ok VARCHAR(3) NOT NULL,
                CHANGE actkey use_activation_key VARCHAR(13) DEFAULT NULL,
                CHANGE regdate use_register_timestamp DATETIME NOT NULL,
                CHANGE roles use_roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\';
            CREATE INDEX IDX_use_username ON use_user (use_username);
            CREATE INDEX IDX_use_active ON use_user (use_active);
            ALTER TABLE use_user ADD PRIMARY KEY (use_id);

            ALTER TABLE upf_user_preference_value DROP PRIMARY KEY;
            ALTER TABLE upf_user_preference_value
                CHANGE uid upf_use_id BIGINT NOT NULL,
                CHANGE prefid upf_usp_id BIGINT NOT NULL,
                CHANGE value upf_value VARCHAR(200) NOT NULL;
            ALTER TABLE upf_user_preference_value ADD CONSTRAINT FRK_upf_use_id
                FOREIGN KEY (upf_use_id) REFERENCES use_user (use_id);
            ALTER TABLE upf_user_preference_value ADD CONSTRAINT FRK_upf_usp_id
                FOREIGN KEY (upf_usp_id) REFERENCES usp_user_preference (usp_id);
            CREATE INDEX IDX_upf_use_id ON upf_user_preference_value (upf_use_id);
            CREATE INDEX IDX_upf_usp_id ON upf_user_preference_value (upf_usp_id);
            ALTER TABLE upf_user_preference_value ADD PRIMARY KEY (upf_use_id, upf_usp_id);

            ALTER TABLE blo_block MODIFY blokid BIGINT NOT NULL;
            ALTER TABLE blo_block DROP PRIMARY KEY;
            ALTER TABLE blo_block
                CHANGE blokid blo_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE parent_block blo_parent_blo_id BIGINT DEFAULT NULL,
                CHANGE name blo_name VARCHAR(55) DEFAULT \'\' NOT NULL,
                CHANGE route blo_route VARCHAR(45) DEFAULT \'\' NOT NULL,
                CHANGE role blo_role VARCHAR(50) DEFAULT NULL,
                CHANGE menu_volgorde blo_menu_order INT DEFAULT 1 NOT NULL,
                CHANGE do_separator blo_do_separator TINYINT(1) NOT NULL;
            ALTER TABLE blo_block ADD CONSTRAINT FRK_blo_parent_blo_id
                FOREIGN KEY (blo_parent_blo_id) REFERENCES blo_block (blo_id);
            CREATE INDEX IDX_blo_parent_blo_id ON blo_block (blo_parent_blo_id);
            ALTER TABLE blo_block ADD PRIMARY KEY (blo_id);

            ALTER TABLE fdw_forum_discussion_wiki MODIFY id BIGINT NOT NULL;
            ALTER TABLE fdw_forum_discussion_wiki DROP PRIMARY KEY;
            ALTER TABLE fdw_forum_discussion_wiki
                CHANGE id fdw_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE discussionid fdw_fod_id BIGINT DEFAULT NULL,
                CHANGE wiki fdw_wiki VARCHAR(50) NOT NULL,
                CHANGE titel fdw_title VARCHAR(50) DEFAULT NULL;
            ALTER TABLE fdw_forum_discussion_wiki ADD CONSTRAINT FRK_fdw_fod_id
                FOREIGN KEY (fdw_fod_id) REFERENCES fod_forum_discussion (fod_id);
            CREATE INDEX IDX_fdw_fod_id ON fdw_forum_discussion_wiki (fdw_fod_id);
            ALTER TABLE fdw_forum_discussion_wiki ADD PRIMARY KEY (fdw_id);

            ALTER TABLE ofo_official_footnote RENAME INDEX idx_ofo_footnote_id TO IDX_ofo_footnote_id;
            ALTER TABLE ofo_official_footnote RENAME INDEX idx_ofo_footnote TO UNQ_ofo_footnote_id_date;

            ALTER TABLE rou_route MODIFY treinid BIGINT NOT NULL;
            ALTER TABLE rou_route DROP PRIMARY KEY;
            ALTER TABLE rou_route
                CHANGE treinid rou_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE treinnr rou_number VARCHAR(15) NOT NULL;
            CREATE UNIQUE INDEX UNQ_rou_number ON rou_route (rou_number);
            ALTER TABLE rou_route ADD PRIMARY KEY (rou_id);

            ALTER TABLE rot_route_train DROP PRIMARY KEY;
            ALTER TABLE rot_route_train
                CHANGE tdr_nr rot_tty_id BIGINT NOT NULL,
                CHANGE treinid rot_rou_id BIGINT NOT NULL,
                CHANGE posid rot_pos_id BIGINT NOT NULL,
                CHANGE spots rot_number_of_spots BIGINT NOT NULL,
                CHANGE dag rot_day_number INT DEFAULT 1 NOT NULL,
                CHANGE mat_pattern_id rot_tnp_id BIGINT DEFAULT NULL;
            ALTER TABLE rot_route_train ADD CONSTRAINT FRK_rot_tty_id
                FOREIGN KEY (rot_tty_id) REFERENCES tty_train_table_year (tty_id);
            ALTER TABLE rot_route_train ADD CONSTRAINT FRK_rot_rou_id
                FOREIGN KEY (rot_rou_id) REFERENCES rou_route (rou_id);
            ALTER TABLE rot_route_train ADD CONSTRAINT FRK_rot_pos_id
                FOREIGN KEY (rot_pos_id) REFERENCES pos_position (pos_id);
            ALTER TABLE rot_route_train ADD CONSTRAINT FRK_rot_tnp_id
                FOREIGN KEY (rot_tnp_id) REFERENCES tnp_train_name_pattern (tnp_id);
            CREATE INDEX IDX_rot_tty_id ON rot_route_train (rot_tty_id);
            CREATE INDEX IDX_rot_rou_id ON rot_route_train (rot_rou_id);
            CREATE INDEX IDX_rot_pos_id ON rot_route_train (rot_pos_id);
            CREATE INDEX IDX_rot_tnp_id ON rot_route_train (rot_tnp_id);
            ALTER TABLE rot_route_train ADD PRIMARY KEY (rot_tty_id, rot_rou_id, rot_pos_id, rot_day_number);

            ALTER TABLE pol_poll MODIFY pollid BIGINT NOT NULL;
            ALTER TABLE pol_poll DROP PRIMARY KEY;
            ALTER TABLE pol_poll
                CHANGE opt_a pol_option_a VARCHAR(150) NOT NULL,
                CHANGE opt_b pol_option_b VARCHAR(150) NOT NULL,
                CHANGE opt_c pol_option_c VARCHAR(150) NOT NULL,
                CHANGE opt_d pol_option_d VARCHAR(150) NOT NULL,
                CHANGE pollid pol_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE question pol_question VARCHAR(200) NOT NULL,
                CHANGE date pol_timestamp DATE NOT NULL;
            ALTER TABLE pol_poll ADD PRIMARY KEY (pol_id);

            ALTER TABLE tct_train_composition_type MODIFY typeid BIGINT NOT NULL;
            ALTER TABLE tct_train_composition_type DROP PRIMARY KEY;
            ALTER TABLE tct_train_composition_type
                CHANGE bak1 tct_car_1 VARCHAR(25) DEFAULT NULL,
                CHANGE bak2 tct_car_2 VARCHAR(25) DEFAULT NULL,
                CHANGE bak3 tct_car_3 VARCHAR(25) DEFAULT NULL,
                CHANGE bak4 tct_car_4 VARCHAR(25) DEFAULT NULL,
                CHANGE bak5 tct_car_5 VARCHAR(25) DEFAULT NULL,
                CHANGE bak6 tct_car_6 VARCHAR(25) DEFAULT NULL,
                CHANGE bak7 tct_car_7 VARCHAR(25) DEFAULT NULL,
                CHANGE bak8 tct_car_8 VARCHAR(25) DEFAULT NULL,
                CHANGE bak9 tct_car_9 VARCHAR(25) DEFAULT NULL,
                CHANGE bak10 tct_car_10 VARCHAR(25) DEFAULT NULL,
                CHANGE bak11 tct_car_11 VARCHAR(25) DEFAULT NULL,
                CHANGE bak12 tct_car_12 VARCHAR(25) DEFAULT NULL,
                CHANGE bak13 tct_car_13 VARCHAR(25) DEFAULT NULL,
                CHANGE typeid tct_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE omschrijving tct_description VARCHAR(25) NOT NULL;
            ALTER TABLE tct_train_composition_type ADD PRIMARY KEY (tct_id);

            ALTER TABLE loc_location MODIFY afkid BIGINT NOT NULL;
            ALTER TABLE loc_location DROP PRIMARY KEY;
            ALTER TABLE loc_location
                CHANGE latitude loc_latitude DOUBLE PRECISION DEFAULT NULL,
                CHANGE longitude loc_longitude DOUBLE PRECISION DEFAULT NULL,
                CHANGE afkid loc_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE landid loc_loa_id BIGINT DEFAULT NULL,
                CHANGE afkorting loc_name VARCHAR(10) NOT NULL,
                CHANGE description loc_description VARCHAR(100) NOT NULL,
                CHANGE traject loc_route_description VARCHAR(15) DEFAULT NULL,
                CHANGE spot_allowed loc_spot_allowed TINYINT(1) NOT NULL,
                CHANGE route_overstaptijd loc_tranfer_time INT DEFAULT NULL;
            ALTER TABLE loc_location ADD CONSTRAINT FRK_loc_loa_id
                FOREIGN KEY (loc_loa_id) REFERENCES loa_location_category (loa_id);
            CREATE INDEX IDX_loc_loa_id ON loc_location (loc_loa_id);
            CREATE INDEX IDX_loc_description ON loc_location (loc_description);
            CREATE UNIQUE INDEX UNQ_loc_name_loa_id ON loc_location (loc_name, loc_loa_id);
            ALTER TABLE loc_location ADD PRIMARY KEY (loc_id);

            ALTER TABLE fpf_forum_post_favorite DROP PRIMARY KEY;
            ALTER TABLE fpf_forum_post_favorite
                CHANGE postid fpf_fop_id BIGINT NOT NULL,
                CHANGE uid fpf_use_id BIGINT NOT NULL;
            ALTER TABLE fpf_forum_post_favorite ADD CONSTRAINT FRK_fpf_fop_id
                FOREIGN KEY (fpf_fop_id) REFERENCES fop_forum_post (fop_id);
            ALTER TABLE fpf_forum_post_favorite ADD CONSTRAINT FRK_fpf_use_id
                FOREIGN KEY (fpf_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_fpf_fop_id ON fpf_forum_post_favorite (fpf_fop_id);
            CREATE INDEX IDX_fpf_use_id ON fpf_forum_post_favorite (fpf_use_id);
            ALTER TABLE fpf_forum_post_favorite ADD PRIMARY KEY (fpf_fop_id, fpf_use_id);

            ALTER TABLE spo_spot MODIFY spotid BIGINT NOT NULL;
            ALTER TABLE spo_spot DROP PRIMARY KEY;
            ALTER TABLE spo_spot
                CHANGE matid spo_tra_id BIGINT DEFAULT NULL,
                CHANGE treinid spo_rou_id BIGINT DEFAULT NULL,
                CHANGE posid spo_pos_id BIGINT DEFAULT NULL,
                CHANGE locatieid spo_loc_id BIGINT DEFAULT NULL,
                CHANGE uid spo_spotter_use_id BIGINT DEFAULT NULL,
                CHANGE timestamp spo_timestamp DATETIME NOT NULL,
                CHANGE datum spo_spot_date DATETIME NOT NULL,
                CHANGE spotid spo_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE input_feedback_flag spo_input_feedback_flag INT NOT NULL;
            ALTER TABLE spo_spot ADD CONSTRAINT FRK_spo_tra_id
                FOREIGN KEY (spo_tra_id) REFERENCES tra_train (tra_id);
            ALTER TABLE spo_spot ADD CONSTRAINT FRK_spo_rou_id
                FOREIGN KEY (spo_rou_id) REFERENCES rou_route (rou_id);
            ALTER TABLE spo_spot ADD CONSTRAINT FRK_spo_pos_id
                FOREIGN KEY (spo_pos_id) REFERENCES pos_position (pos_id);
            ALTER TABLE spo_spot ADD CONSTRAINT FRK_spo_loc_id
                FOREIGN KEY (spo_loc_id) REFERENCES loc_location (loc_id);
            ALTER TABLE spo_spot ADD CONSTRAINT FRK_spo_spotter_use_id
                FOREIGN KEY (spo_spotter_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_spo_rou_id ON spo_spot (spo_rou_id);
            CREATE INDEX IDX_spo_pos_id ON spo_spot (spo_pos_id);
            CREATE INDEX IDX_spo_loc_id ON spo_spot (spo_loc_id);
            CREATE INDEX IDX_spo_tra_id ON spo_spot (spo_tra_id);
            CREATE INDEX IDX_spo_timestamp ON spo_spot (spo_timestamp);
            CREATE INDEX IDX_spo_spotter_use_id ON spo_spot (spo_spotter_use_id);
            CREATE UNIQUE INDEX UNQ_spo_tra_id_pos_id_loc_id_rou_id_spotter_use_id_timestamp
                ON spo_spot (spo_tra_id, spo_pos_id, spo_loc_id, spo_rou_id, spo_spotter_use_id, spo_timestamp);
            ALTER TABLE spo_spot ADD PRIMARY KEY (spo_id);

            ALTER TABLE foc_forum_category MODIFY catid BIGINT NOT NULL;
            ALTER TABLE foc_forum_category DROP PRIMARY KEY;
            ALTER TABLE foc_forum_category
                CHANGE catid foc_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE name foc_name VARCHAR(30) NOT NULL,
                CHANGE volgorde foc_order INT DEFAULT 1 NOT NULL;
            ALTER TABLE foc_forum_category ADD PRIMARY KEY (foc_id);

            ALTER TABLE tra_train MODIFY matid BIGINT NOT NULL;
            ALTER TABLE tra_train DROP PRIMARY KEY;
            ALTER TABLE tra_train
                CHANGE vervoerder_id tra_trn_id BIGINT DEFAULT NULL,
                CHANGE pattern_id tra_tnp_id BIGINT DEFAULT NULL,
                CHANGE matid tra_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE nummer tra_number VARCHAR(20) NOT NULL,
                CHANGE naam tra_name VARCHAR(35) DEFAULT NULL;
            ALTER TABLE tra_train ADD CONSTRAINT FRK_tra_trn_id
                FOREIGN KEY (tra_trn_id) REFERENCES trn_transporter (trn_id);
            ALTER TABLE tra_train ADD CONSTRAINT FRK_tra_tnp_id
                FOREIGN KEY (tra_tnp_id) REFERENCES tnp_train_name_pattern (tnp_id);
            CREATE INDEX IDX_tra_tnp_id ON tra_train (tra_tnp_id);
            CREATE INDEX IDX_tra_trn_id ON tra_train (tra_trn_id);
            CREATE UNIQUE INDEX UNQ_tra_number ON tra_train (tra_number);
            ALTER TABLE tra_train ADD PRIMARY KEY (tra_id);

            ALTER TABLE fsw_forum_search_word MODIFY woord_id BIGINT NOT NULL;
            ALTER TABLE fsw_forum_search_word DROP PRIMARY KEY;
            ALTER TABLE fsw_forum_search_word
                CHANGE woord_id fsw_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE woord fsw_word VARCHAR(50) NOT NULL;
            CREATE UNIQUE INDEX UNQ_fsw_word ON fsw_forum_search_word (fsw_word);
            ALTER TABLE fsw_forum_search_word ADD PRIMARY KEY (fsw_id);

            ALTER TABLE dda_ddar MODIFY id BIGINT NOT NULL;
            ALTER TABLE dda_ddar DROP PRIMARY KEY;
            ALTER TABLE dda_ddar
                CHANGE matid dda_tra_id BIGINT DEFAULT NULL,
                CHANGE afkid dda_loc_id BIGINT DEFAULT NULL,
                CHANGE spot_ander_laatste dda_timestamp_other_last DATE DEFAULT NULL,
                CHANGE spot_laatste dda_timestamp_last DATE DEFAULT NULL,
                CHANGE spot_ander_eerste dda_timestamp_other_first DATE DEFAULT NULL,
                CHANGE id dda_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE stam dda_trunk_number INT DEFAULT NULL,
                CHANGE spot_eerste dda_timestamp_first DATE NOT NULL,
                CHANGE extra dda_extra VARCHAR(150) NOT NULL;
            ALTER TABLE dda_ddar ADD CONSTRAINT FRK_dda_tra_id
                FOREIGN KEY (dda_tra_id) REFERENCES tra_train (tra_id);
            ALTER TABLE dda_ddar ADD CONSTRAINT FRK_dda_loc_id
                FOREIGN KEY (dda_loc_id) REFERENCES loc_location (loc_id);
            CREATE INDEX IDX_dda_loc_id ON dda_ddar (dda_loc_id);
            CREATE INDEX IDX_dda_tra_id ON dda_ddar (dda_tra_id);
            ALTER TABLE dda_ddar ADD PRIMARY KEY (dda_id);

            ALTER TABLE loa_location_category MODIFY verk_catid BIGINT NOT NULL;
            ALTER TABLE loa_location_category DROP PRIMARY KEY;
            ALTER TABLE loa_location_category
                CHANGE verk_catid loa_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE code loa_code VARCHAR(5) NOT NULL,
                CHANGE name loa_name VARCHAR(20) NOT NULL;
            ALTER TABLE loa_location_category ADD PRIMARY KEY (loa_id);

            ALTER TABLE rod_route_operation_days MODIFY rijdagenid BIGINT NOT NULL;
            ALTER TABLE rod_route_operation_days DROP PRIMARY KEY;
            ALTER TABLE rod_route_operation_days
                CHANGE ma rod_monday TINYINT(1) NOT NULL,
                CHANGE di rod_tuesday TINYINT(1) NOT NULL,
                CHANGE wo rod_wednesday TINYINT(1) NOT NULL,
                CHANGE do rod_thursday TINYINT(1) NOT NULL,
                CHANGE vr rod_friday TINYINT(1) NOT NULL,
                CHANGE za rod_saturday TINYINT(1) NOT NULL,
                CHANGE zf rod_sunday TINYINT(1) NOT NULL,
                CHANGE rijdagenid rod_id BIGINT AUTO_INCREMENT NOT NULL;
            ALTER TABLE rod_route_operation_days ADD PRIMARY KEY (rod_id);

            ALTER TABLE rll_route_list_location DROP PRIMARY KEY;
            ALTER TABLE rll_route_list_location
                CHANGE type rll_type INT DEFAULT 1 NOT NULL,
                CHANGE volgorde rll_order INT DEFAULT 1 NOT NULL,
                CHANGE tdr_nr rll_tty_id BIGINT NOT NULL,
                CHANGE treinnummerlijst_id rll_rol_id BIGINT NOT NULL,
                CHANGE locatieid rll_loc_id BIGINT DEFAULT NULL;
            ALTER TABLE rll_route_list_location ADD CONSTRAINT FRK_rll_tty_id
                FOREIGN KEY (rll_tty_id) REFERENCES tty_train_table_year (tty_id);
            ALTER TABLE rll_route_list_location ADD CONSTRAINT FRK_rll_rol_id
                FOREIGN KEY (rll_rol_id) REFERENCES rol_route_list (rol_id);
            ALTER TABLE rll_route_list_location ADD CONSTRAINT FRK_rll_loc_id
                FOREIGN KEY (rll_loc_id) REFERENCES loc_location (loc_id);
            CREATE INDEX IDX_rll_tty_id ON rll_route_list_location (rll_tty_id);
            CREATE INDEX IDX_rll_rol_id ON rll_route_list_location (rll_rol_id);
            CREATE INDEX IDX_rll_loc_id ON rll_route_list_location (rll_loc_id);
            ALTER TABLE rll_route_list_location ADD PRIMARY KEY (rll_tty_id, rll_rol_id, rll_type, rll_order);

            ALTER TABLE foa_forum_favorite DROP PRIMARY KEY;
            ALTER TABLE foa_forum_favorite
                CHANGE discussionid foa_fod_id BIGINT NOT NULL,
                CHANGE uid foa_use_id BIGINT NOT NULL,
                CHANGE alerting foa_alerting INT DEFAULT 0 NOT NULL;
            ALTER TABLE foa_forum_favorite ADD CONSTRAINT FRK_foa_fod_id
                FOREIGN KEY (foa_fod_id) REFERENCES fod_forum_discussion (fod_id);
            ALTER TABLE foa_forum_favorite ADD CONSTRAINT FRK_foa_use_id
                FOREIGN KEY (foa_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_foa_fod_id ON foa_forum_favorite (foa_fod_id);
            CREATE INDEX IDX_foa_use_id ON foa_forum_favorite (foa_use_id);
            ALTER TABLE foa_forum_favorite ADD PRIMARY KEY (foa_fod_id, foa_use_id);

            ALTER TABLE pon_point_of_interest_category MODIFY provincieid BIGINT NOT NULL;
            ALTER TABLE pon_point_of_interest_category DROP PRIMARY KEY;
            ALTER TABLE pon_point_of_interest_category
                CHANGE provincieid pon_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE naam pon_name VARCHAR(15) NOT NULL;
            ALTER TABLE pon_point_of_interest_category ADD PRIMARY KEY (pon_id);

            ALTER TABLE blh_block_help DROP PRIMARY KEY;
            ALTER TABLE blh_block_help
                CHANGE text blh_text TEXT NOT NULL,
                CHANGE ad_code blh_ad_code TEXT NOT NULL,
                CHANGE blokid blh_blo_id BIGINT NOT NULL,
                CHANGE google_channel blh_google_channel VARCHAR(10) NOT NULL;
            ALTER TABLE blh_block_help ADD CONSTRAINT FRK_blh_blo_id
                FOREIGN KEY (blh_blo_id) REFERENCES blo_block (blo_id);
            ALTER TABLE blh_block_help ADD PRIMARY KEY (blh_blo_id);

            ALTER TABLE sta_statistic MODIFY id BIGINT NOT NULL;
            ALTER TABLE sta_statistic DROP PRIMARY KEY;
            ALTER TABLE sta_statistic
                CHANGE uniek sta_visitors_unique BIGINT NOT NULL,
                CHANGE pageviews sta_visitors_total BIGINT NOT NULL,
                CHANGE pageviews_home sta_visitors_home BIGINT NOT NULL,
                CHANGE pageviews_func sta_visitor_functions BIGINT NOT NULL,
                CHANGE spots sta_number_of_spots BIGINT NOT NULL,
                CHANGE posts sta_number_of_posts BIGINT NOT NULL,
                CHANGE id sta_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE datum sta_timestamp DATETIME NOT NULL;
            CREATE UNIQUE INDEX UNQ_sta_timestamp ON sta_statistic (sta_timestamp);
            ALTER TABLE sta_statistic ADD PRIMARY KEY (sta_id);

            ALTER TABLE fpt_forum_post_text DROP PRIMARY KEY;
            ALTER TABLE fpt_forum_post_text
                CHANGE postid fpt_fop_id BIGINT NOT NULL,
                CHANGE new_style fpt_new_style TINYINT(1) DEFAULT \'1\' NOT NULL,
                CHANGE text fpt_text LONGTEXT NOT NULL;
            ALTER TABLE fpt_forum_post_text ADD CONSTRAINT FRK_fpt_fop_id
                FOREIGN KEY (fpt_fop_id) REFERENCES fop_forum_post (fop_id);
            ALTER TABLE fpt_forum_post_text ADD PRIMARY KEY (fpt_fop_id);

            ALTER TABLE poi_point_of_interest MODIFY puntid BIGINT NOT NULL;
            ALTER TABLE poi_point_of_interest DROP PRIMARY KEY;
            ALTER TABLE poi_point_of_interest
                CHANGE afkid_locatie poi_loc_id BIGINT DEFAULT NULL,
                CHANGE afkid_traject_1 poi_section_1_loc_id BIGINT DEFAULT NULL,
                CHANGE afkid_traject_2 poi_section_2_loc_id BIGINT DEFAULT NULL,
                CHANGE afkid_dks poi_routes_loc_id BIGINT DEFAULT NULL,
                CHANGE provincieid poi_pon_id BIGINT DEFAULT NULL,
                CHANGE kilometrering poi_kilometre VARCHAR(25) DEFAULT NULL,
                CHANGE gps poi_gps VARCHAR(25) DEFAULT NULL,
                CHANGE zonstand_winter poi_sun_position_winter VARCHAR(50) DEFAULT NULL,
                CHANGE zonstand_zomer poi_sun_position_summer VARCHAR(50) DEFAULT NULL,
                CHANGE puntid poi_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE naam poi_name VARCHAR(50) NOT NULL,
                CHANGE google_url poi_google_url VARCHAR(200) DEFAULT NULL,
                CHANGE foto poi_photo VARCHAR(25) DEFAULT \'geen_foto.jpg\' NOT NULL;
            ALTER TABLE poi_point_of_interest ADD CONSTRAINT FRK_poi_loc_id
                FOREIGN KEY (poi_loc_id) REFERENCES loc_location (loc_id);
            ALTER TABLE poi_point_of_interest ADD CONSTRAINT FRK_poi_section_1_loc_id
                FOREIGN KEY (poi_section_1_loc_id) REFERENCES loc_location (loc_id);
            ALTER TABLE poi_point_of_interest ADD CONSTRAINT FRK_poi_section_2_loc_id
                FOREIGN KEY (poi_section_2_loc_id) REFERENCES loc_location (loc_id);
            ALTER TABLE poi_point_of_interest ADD CONSTRAINT FRK_poi_routes_loc_id
                FOREIGN KEY (poi_routes_loc_id) REFERENCES loc_location (loc_id);
            ALTER TABLE poi_point_of_interest ADD CONSTRAINT FRK_poi_pon_id
                FOREIGN KEY (poi_pon_id) REFERENCES pon_point_of_interest_category (pon_id);
            CREATE INDEX IDX_poi_loc_id ON poi_point_of_interest (poi_loc_id);
            CREATE INDEX IDX_poi_section_1_loc_id ON poi_point_of_interest (poi_section_1_loc_id);
            CREATE INDEX IDX_poi_section_2_loc_id ON poi_point_of_interest (poi_section_2_loc_id);
            CREATE INDEX IDX_poi_routes_loc_id ON poi_point_of_interest (poi_routes_loc_id);
            CREATE INDEX IDX_poi_pon_id ON poi_point_of_interest (poi_pon_id);
            ALTER TABLE poi_point_of_interest ADD PRIMARY KEY (poi_id);

            ALTER TABLE trc_train_composition MODIFY matsmsid BIGINT NOT NULL;
            ALTER TABLE trc_train_composition DROP PRIMARY KEY;
            ALTER TABLE trc_train_composition
                CHANGE bak1 trc_car_1 VARCHAR(15) DEFAULT NULL,
                CHANGE bak2 trc_car_2 VARCHAR(15) DEFAULT NULL,
                CHANGE bak3 trc_car_3 VARCHAR(15) DEFAULT NULL,
                CHANGE bak4 trc_car_4 VARCHAR(15) DEFAULT NULL,
                CHANGE bak5 trc_car_5 VARCHAR(15) DEFAULT NULL,
                CHANGE bak6 trc_car_6 VARCHAR(15) DEFAULT NULL,
                CHANGE bak7 trc_car_7 VARCHAR(15) DEFAULT NULL,
                CHANGE bak8 trc_car_8 VARCHAR(15) DEFAULT NULL,
                CHANGE bak9 trc_car_9 VARCHAR(15) DEFAULT NULL,
                CHANGE bak10 trc_car_10 VARCHAR(15) DEFAULT NULL,
                CHANGE bak11 trc_car_11 VARCHAR(15) DEFAULT NULL,
                CHANGE bak12 trc_car_12 VARCHAR(15) DEFAULT NULL,
                CHANGE bak13 trc_car_13 VARCHAR(15) DEFAULT NULL,
                CHANGE opmerkingen trc_note VARCHAR(255) DEFAULT NULL,
                CHANGE extra trc_extra VARCHAR(255) DEFAULT NULL,
                CHANGE matsmsid trc_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE typeid trc_tct_id BIGINT DEFAULT NULL,
                CHANGE last_update trc_last_update_timestamp DATETIME DEFAULT NULL,
                CHANGE index_regel trc_index_line TINYINT(1) NOT NULL;
            ALTER TABLE trc_train_composition ADD CONSTRAINT FRK_trc_tct_id
                FOREIGN KEY (trc_tct_id) REFERENCES tct_train_composition_type (tct_id);
            CREATE INDEX IDX_trc_tct_id ON trc_train_composition (trc_tct_id);
            ALTER TABLE trc_train_composition ADD PRIMARY KEY (trc_id);

            ALTER TABLE spr_special_route MODIFY drglid BIGINT NOT NULL;
            ALTER TABLE spr_special_route DROP PRIMARY KEY;
            ALTER TABLE spr_special_route
                CHANGE werkzaamheden spr_construction TINYINT(1) NOT NULL,
                CHANGE public spr_public TINYINT(1) NOT NULL,
                CHANGE drglid spr_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE pubdatum spr_publication_timestamp DATETIME DEFAULT NULL,
                CHANGE datum spr_start_date DATE NOT NULL,
                CHANGE einddatum spr_end_date DATE DEFAULT NULL,
                CHANGE title spr_title VARCHAR(75) NOT NULL,
                CHANGE image spr_image VARCHAR(20) NOT NULL,
                CHANGE text spr_text LONGTEXT NOT NULL;
            ALTER TABLE spr_special_route ADD PRIMARY KEY (spr_id);

            ALTER TABLE srr_special_route_read DROP PRIMARY KEY;
            ALTER TABLE srr_special_route_read
                CHANGE drglid srr_spr_id BIGINT NOT NULL,
                CHANGE uid srr_use_id BIGINT NOT NULL;
            ALTER TABLE srr_special_route_read ADD CONSTRAINT FRK_srr_spr_id
                FOREIGN KEY (srr_spr_id) REFERENCES spr_special_route (spr_id);
            ALTER TABLE srr_special_route_read ADD CONSTRAINT FRK_srr_use_id
                FOREIGN KEY (srr_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_18CE5B1A371B5998 ON srr_special_route_read (srr_spr_id);
            CREATE INDEX IDX_18CE5B1A6B2CAF73 ON srr_special_route_read (srr_use_id);
            ALTER TABLE srr_special_route_read ADD PRIMARY KEY (srr_spr_id, srr_use_id);

            ALTER TABLE rol_route_list MODIFY id BIGINT NOT NULL;
            ALTER TABLE rol_route_list DROP PRIMARY KEY;
            ALTER TABLE rol_route_list
                CHANGE tdr_nr rol_tty_id BIGINT DEFAULT NULL,
                CHANGE vervoerder_id rol_trn_id BIGINT DEFAULT NULL,
                CHANGE karakteristiek_id rol_cha_id BIGINT DEFAULT NULL,
                CHANGE id rol_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE nr_start rol_first_number INT DEFAULT 1 NOT NULL,
                CHANGE nr_eind rol_last_number INT DEFAULT 2 NOT NULL,
                CHANGE traject rol_section VARCHAR(75) DEFAULT NULL;
            ALTER TABLE rol_route_list ADD CONSTRAINT FRK_rol_tty_id
                FOREIGN KEY (rol_tty_id) REFERENCES tty_train_table_year (tty_id);
            ALTER TABLE rol_route_list ADD CONSTRAINT FRK_rol_trn_id
                FOREIGN KEY (rol_trn_id) REFERENCES trn_transporter (trn_id);
            ALTER TABLE rol_route_list ADD CONSTRAINT FRK_rol_cha_id
                FOREIGN KEY (rol_cha_id) REFERENCES cha_characteristic (cha_id);
            CREATE INDEX IDX_rol_tty_id ON rol_route_list (rol_tty_id);
            CREATE INDEX IDX_rol_trn_id ON rol_route_list (rol_trn_id);
            CREATE INDEX IDX_rol_cha_id ON rol_route_list (rol_cha_id);
            CREATE INDEX IDX_rol_first_number ON rol_route_list (rol_first_number);
            CREATE INDEX IDX_rol_last_number ON rol_route_list (rol_last_number);
            ALTER TABLE rol_route_list ADD PRIMARY KEY (rol_id);

            ALTER TABLE rlr_route_list_route DROP PRIMARY KEY;
            ALTER TABLE rlr_route_list_route
                CHANGE treinnummerlijst_id rlr_rol_id BIGINT NOT NULL,
                CHANGE treinid rlr_rou_id BIGINT NOT NULL;
            ALTER TABLE rlr_route_list_route ADD CONSTRAINT FRK_rlr_rol_id
                FOREIGN KEY (rlr_rol_id) REFERENCES rol_route_list (rol_id);
            ALTER TABLE rlr_route_list_route ADD CONSTRAINT FRK_rlr_rou_id
                FOREIGN KEY (rlr_rou_id) REFERENCES rou_route (rou_id);
            CREATE INDEX IDX_F82E393D25F1581F ON rlr_route_list_route (rlr_rol_id);
            CREATE INDEX IDX_F82E393D9D6464A8 ON rlr_route_list_route (rlr_rou_id);
            ALTER TABLE rlr_route_list_route ADD PRIMARY KEY (rlr_rol_id, rlr_rou_id);

            ALTER TABLE fod_forum_discussion MODIFY discussionid BIGINT NOT NULL;
            ALTER TABLE fod_forum_discussion DROP PRIMARY KEY;
            ALTER TABLE fod_forum_discussion
                CHANGE forumid fod_fof_id BIGINT DEFAULT NULL,
                CHANGE authorid fod_author_use_id BIGINT DEFAULT NULL,
                CHANGE discussionid fod_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE title fod_title VARCHAR(50) NOT NULL,
                CHANGE viewed fod_viewed BIGINT NOT NULL,
                CHANGE locked fod_locked TINYINT(1) DEFAULT \'0\' NOT NULL;
            ALTER TABLE fod_forum_discussion ADD CONSTRAINT FRK_fod_fof_id
                FOREIGN KEY (fod_fof_id) REFERENCES fof_forum_forum (fof_id);
            ALTER TABLE fod_forum_discussion ADD CONSTRAINT FRK_fod_author_use_id
                FOREIGN KEY (fod_author_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_fod_author_use_id ON fod_forum_discussion (fod_author_use_id);
            CREATE INDEX IDX_fod_fof_id ON fod_forum_discussion (fod_fof_id);
            ALTER TABLE fod_forum_discussion ADD PRIMARY KEY (fod_id);

            ALTER TABLE pos_position MODIFY posid BIGINT NOT NULL;
            ALTER TABLE pos_position DROP PRIMARY KEY;
            ALTER TABLE pos_position
                CHANGE posid pos_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE positie pos_name VARCHAR(2) NOT NULL;
            ALTER TABLE pos_position ADD PRIMARY KEY (pos_id);

            ALTER TABLE fof_forum_forum MODIFY forumid BIGINT NOT NULL;
            ALTER TABLE fof_forum_forum DROP PRIMARY KEY;
            ALTER TABLE fof_forum_forum
                CHANGE volgorde fof_order INT DEFAULT 1 NOT NULL,
                CHANGE type fof_type INT DEFAULT 1 NOT NULL,
                CHANGE forumid fof_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE catid fof_foc_id BIGINT DEFAULT NULL,
                CHANGE name fof_name VARCHAR(40) NOT NULL,
                CHANGE description fof_description VARCHAR(100) NOT NULL;
            ALTER TABLE fof_forum_forum ADD CONSTRAINT FRK_fof_foc_id
                FOREIGN KEY (fof_foc_id) REFERENCES foc_forum_category (foc_id);
            ALTER TABLE fof_forum_forum ADD PRIMARY KEY (fof_id);
            CREATE INDEX IDX_fof_foc_id ON fof_forum_forum (fof_foc_id);

            ALTER TABLE ffm_forum_forum_moderator DROP PRIMARY KEY;
            ALTER TABLE ffm_forum_forum_moderator
                CHANGE forumid ffm_fof_id BIGINT NOT NULL,
                CHANGE uid ffm_use_id BIGINT NOT NULL;
            ALTER TABLE ffm_forum_forum_moderator ADD CONSTRAINT FRK_ffm_fof_id
                FOREIGN KEY (ffm_fof_id) REFERENCES fof_forum_forum (fof_id);
            ALTER TABLE ffm_forum_forum_moderator ADD CONSTRAINT FRK_ffm_use_id
                FOREIGN KEY (ffm_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_ffm_fof_id ON ffm_forum_forum_moderator (ffm_fof_id);
            CREATE INDEX IDX_ffm_use_id ON ffm_forum_forum_moderator (ffm_use_id);
            ALTER TABLE ffm_forum_forum_moderator ADD PRIMARY KEY (ffm_fof_id, ffm_use_id);

            ALTER TABLE log_log MODIFY logid BIGINT NOT NULL;
            ALTER TABLE log_log DROP PRIMARY KEY;
            ALTER TABLE log_log
                CHANGE duration log_duration DOUBLE PRECISION DEFAULT NULL,
                CHANGE memory_usage log_memory_usage DOUBLE PRECISION DEFAULT NULL,
                CHANGE logid log_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE uid log_use_id BIGINT DEFAULT NULL,
                CHANGE datumtijd log_timestamp DATETIME NOT NULL,
                CHANGE ip log_ip_address BIGINT NOT NULL,
                CHANGE route log_route VARCHAR(255) NOT NULL,
                CHANGE route_parameters log_route_parameters LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\';
            ALTER TABLE log_log ADD CONSTRAINT FRK_log_use_id
                FOREIGN KEY (log_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_log_use_id ON log_log (log_use_id);
            ALTER TABLE log_log ADD PRIMARY KEY (log_id);

            ALTER TABLE bcu_banner_customer_user DROP PRIMARY KEY;
            ALTER TABLE bcu_banner_customer_user
                CHANGE id bcu_bac_id BIGINT NOT NULL,
                CHANGE uid bcu_use_id BIGINT NOT NULL,
                CHANGE allowed_new bcu_allowed_new TINYINT(1) DEFAULT \'0\' NOT NULL,
                CHANGE allowed_max_views bcu_allowed_max_views TINYINT(1) DEFAULT \'0\' NOT NULL,
                CHANGE allowed_max_hits bcu_allowed_max_hits TINYINT(1) DEFAULT \'0\' NOT NULL,
                CHANGE allowed_max_date bcu_allowed_max_date TINYINT(1) DEFAULT \'0\' NOT NULL,
                CHANGE allowed_deactivate bcu_allowed_deactivate TINYINT(1) DEFAULT \'0\' NOT NULL;
            ALTER TABLE bcu_banner_customer_user ADD CONSTRAINT FRK_bcu_bac_id
                FOREIGN KEY (bcu_bac_id) REFERENCES bac_banner_customer (bac_id);
            ALTER TABLE bcu_banner_customer_user ADD CONSTRAINT FRK_bcu_use_id
                FOREIGN KEY (bcu_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_bcu_bac_id ON bcu_banner_customer_user (bcu_bac_id);
            CREATE INDEX IDX_bcu_use_id ON bcu_banner_customer_user (bcu_use_id);
            ALTER TABLE bcu_banner_customer_user ADD PRIMARY KEY (bcu_bac_id, bcu_use_id);

            ALTER TABLE tnp_train_name_pattern MODIFY id BIGINT NOT NULL;
            ALTER TABLE tnp_train_name_pattern DROP PRIMARY KEY;
            ALTER TABLE tnp_train_name_pattern
                CHANGE id tnp_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE volgorde tnp_order INT DEFAULT 1 NOT NULL,
                CHANGE pattern tnp_pattern VARCHAR(80) DEFAULT \'\' NOT NULL,
                CHANGE naam tnp_name VARCHAR(50) DEFAULT \'\' NOT NULL,
                CHANGE tekening tnp_image VARCHAR(30) DEFAULT NULL;
            CREATE UNIQUE INDEX UNQ_tnp_order ON tnp_train_name_pattern (tnp_order);
            ALTER TABLE tnp_train_name_pattern ADD PRIMARY KEY (tnp_id);

            ALTER TABLE tcp_train_composition_proposition DROP PRIMARY KEY;
            ALTER TABLE tcp_train_composition_proposition
                CHANGE matsmsid tcp_trc_id BIGINT NOT NULL,
                CHANGE uid tcp_use_id BIGINT NOT NULL,
                CHANGE bak1 tcp_car_1 VARCHAR(15) DEFAULT NULL,
                CHANGE bak2 tcp_car_2 VARCHAR(15) DEFAULT NULL,
                CHANGE bak3 tcp_car_3 VARCHAR(15) DEFAULT NULL,
                CHANGE bak4 tcp_car_4 VARCHAR(15) DEFAULT NULL,
                CHANGE bak5 tcp_car_5 VARCHAR(15) DEFAULT NULL,
                CHANGE bak6 tcp_car_6 VARCHAR(15) DEFAULT NULL,
                CHANGE bak7 tcp_car_7 VARCHAR(15) DEFAULT NULL,
                CHANGE bak8 tcp_car_8 VARCHAR(15) DEFAULT NULL,
                CHANGE bak9 tcp_car_9 VARCHAR(15) DEFAULT NULL,
                CHANGE bak10 tcp_car_10 VARCHAR(15) DEFAULT NULL,
                CHANGE bak11 tcp_car_11 VARCHAR(15) DEFAULT NULL,
                CHANGE bak12 tcp_car_12 VARCHAR(15) DEFAULT NULL,
                CHANGE bak13 tcp_car_13 VARCHAR(15) DEFAULT NULL,
                CHANGE datum tcp_timestamp DATETIME NOT NULL,
                CHANGE opmerkingen tcp_note VARCHAR(255) DEFAULT NULL;
            ALTER TABLE tcp_train_composition_proposition ADD CONSTRAINT FRK_tcp_trc_id
                FOREIGN KEY (tcp_trc_id) REFERENCES trc_train_composition (trc_id);
            ALTER TABLE tcp_train_composition_proposition ADD CONSTRAINT FRK_tcp_use_id
                FOREIGN KEY (tcp_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_tcp_trc_id ON tcp_train_composition_proposition (tcp_trc_id);
            CREATE INDEX IDX_tcp_use_id ON tcp_train_composition_proposition (tcp_use_id);
            ALTER TABLE tcp_train_composition_proposition ADD PRIMARY KEY (tcp_trc_id, tcp_use_id);

            ALTER TABLE bav_banner_view MODIFY id BIGINT NOT NULL;
            ALTER TABLE bav_banner_view DROP PRIMARY KEY;
            ALTER TABLE bav_banner_view
                CHANGE id bav_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE bannerid bav_ban_id BIGINT DEFAULT NULL,
                CHANGE timestamp bav_timestamp DATETIME NOT NULL,
                CHANGE ip bav_ip_address BIGINT NOT NULL;
            ALTER TABLE bav_banner_view ADD CONSTRAINT FRK_bav_ban_id
                FOREIGN KEY (bav_ban_id) REFERENCES ban_banner (ban_id);
            CREATE INDEX IDX_bav_ban_id ON bav_banner_view (bav_ban_id);
            ALTER TABLE bav_banner_view ADD PRIMARY KEY (bav_id);

            ALTER TABLE rns_rail_news_source MODIFY snb_id BIGINT NOT NULL;
            ALTER TABLE rns_rail_news_source DROP PRIMARY KEY;
            ALTER TABLE rns_rail_news_source
                CHANGE snb_id rns_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE snb_bron rns_name VARCHAR(7) NOT NULL,
                CHANGE snb_logo rns_logo VARCHAR(25) NOT NULL,
                CHANGE snb_url rns_url VARCHAR(30) NOT NULL,
                CHANGE snb_description rns_description VARCHAR(100) NOT NULL;
            ALTER TABLE rns_rail_news_source ADD PRIMARY KEY (rns_id);

            ALTER TABLE tty_train_table_year MODIFY tdr_nr BIGINT NOT NULL;
            ALTER TABLE tty_train_table_year DROP PRIMARY KEY;
            ALTER TABLE tty_train_table_year
                CHANGE start_datum tty_start_date DATE NOT NULL,
                CHANGE eind_datum tty_end_date DATE NOT NULL,
                CHANGE tdr_nr tty_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE naam tty_name VARCHAR(10) NOT NULL;
            ALTER TABLE tty_train_table_year ADD PRIMARY KEY (tty_id);

            ALTER TABLE spe_spot_extra DROP PRIMARY KEY;
            ALTER TABLE spe_spot_extra
                CHANGE extra spe_extra VARCHAR(255) NOT NULL,
                CHANGE user_extra spe_user_extra VARCHAR(255) NOT NULL,
                CHANGE spotid spe_spo_id BIGINT NOT NULL;
            ALTER TABLE spe_spot_extra ADD CONSTRAINT FRK_spe_spo_id
                FOREIGN KEY (spe_spo_id) REFERENCES spo_spot (spo_id);
            ALTER TABLE spe_spot_extra ADD PRIMARY KEY (spe_spo_id);

            ALTER TABLE fpa_forum_post_alert MODIFY id BIGINT NOT NULL;
            ALTER TABLE fpa_forum_post_alert DROP PRIMARY KEY;
            ALTER TABLE fpa_forum_post_alert
                CHANGE postid fpa_fop_id BIGINT DEFAULT NULL,
                CHANGE senderid fpa_sender_use_id BIGINT DEFAULT NULL,
                CHANGE id fpa_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE closed fpa_closed TINYINT(1) NOT NULL,
                CHANGE timestamp fpa_timestamp DATETIME NOT NULL,
                CHANGE comment fpa_comment LONGTEXT DEFAULT NULL;
            ALTER TABLE fpa_forum_post_alert ADD CONSTRAINT FRK_fpa_fop_id
                FOREIGN KEY (fpa_fop_id) REFERENCES fop_forum_post (fop_id);
            ALTER TABLE fpa_forum_post_alert ADD CONSTRAINT FRK_fpa_sender_use_id
                FOREIGN KEY (fpa_sender_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_fpa_sender_use_id ON fpa_forum_post_alert (fpa_sender_use_id);
            CREATE INDEX IDX_fpa_fop_id ON fpa_forum_post_alert (fpa_fop_id);
            ALTER TABLE fpa_forum_post_alert ADD PRIMARY KEY (fpa_id);

            ALTER TABLE stb_statistic_block DROP PRIMARY KEY;
            ALTER TABLE stb_statistic_block
                CHANGE blokid stb_blo_id BIGINT NOT NULL,
                CHANGE pageviews stb_views BIGINT NOT NULL,
                CHANGE date stb_date DATE NOT NULL;
            ALTER TABLE stb_statistic_block ADD CONSTRAINT FRK_stb_blo_id
                FOREIGN KEY (stb_blo_id) REFERENCES blo_block (blo_id);
            CREATE INDEX IDX_stb_blo_id ON stb_statistic_block (stb_blo_id);
            ALTER TABLE stb_statistic_block ADD PRIMARY KEY (stb_blo_id, stb_date);

            ALTER TABLE usi_user_info DROP PRIMARY KEY;
            ALTER TABLE usi_user_info
                CHANGE bedrijf_id usi_usc_id BIGINT DEFAULT NULL,
                CHANGE mob_tel usi_mobile_phone BIGINT DEFAULT NULL,
                CHANGE twitter_account usi_twitter_account VARCHAR(255) DEFAULT NULL,
                CHANGE facebook_account usi_facebook_account VARCHAR(255) DEFAULT NULL,
                CHANGE flickr_account usi_flickr_account VARCHAR(255) DEFAULT NULL,
                CHANGE youtube_account usi_youtube_account VARCHAR(255) DEFAULT NULL,
                CHANGE uid usi_use_id BIGINT NOT NULL,
                CHANGE avatar usi_avatar VARCHAR(30) DEFAULT \'_blank.png\' NOT NULL,
                CHANGE website usi_website VARCHAR(75) DEFAULT NULL,
                CHANGE city usi_city VARCHAR(50) DEFAULT NULL,
                CHANGE skype usi_skype VARCHAR(60) DEFAULT NULL,
                CHANGE geslacht usi_gender SMALLINT DEFAULT 0 NOT NULL,
                CHANGE gebdatum usi_birth_date DATE DEFAULT NULL;
            ALTER TABLE usi_user_info ADD CONSTRAINT FRK_usi_use_id
                FOREIGN KEY (usi_use_id) REFERENCES use_user (use_id);
            ALTER TABLE usi_user_info ADD CONSTRAINT FRK_usi_usc_id
                FOREIGN KEY (usi_usc_id) REFERENCES usc_user_company (usc_id);
            CREATE INDEX IDX_usi_usc_id ON usi_user_info (usi_usc_id);
            CREATE INDEX IDX_usi_birth_date ON usi_user_info (usi_birth_date);
            ALTER TABLE usi_user_info ADD PRIMARY KEY (usi_use_id);

            ALTER TABLE ban_banner MODIFY bannerid BIGINT NOT NULL;
            ALTER TABLE ban_banner DROP PRIMARY KEY;
            ALTER TABLE ban_banner
                CHANGE link ban_link VARCHAR(100) NOT NULL,
                CHANGE max_views ban_max_views INT DEFAULT 0 NOT NULL,
                CHANGE max_hits ban_max_hits INT DEFAULT 0 NOT NULL,
                CHANGE start_date ban_start_date DATETIME DEFAULT NULL,
                CHANGE end_date ban_end_date DATETIME DEFAULT NULL,
                CHANGE bannerid ban_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE customerid ban_bac_id BIGINT DEFAULT NULL,
                CHANGE code ban_code VARCHAR(6) DEFAULT NULL,
                CHANGE active ban_active TINYINT(1) DEFAULT \'0\' NOT NULL,
                CHANGE location ban_location VARCHAR(6) DEFAULT \'header\' NOT NULL,
                CHANGE description ban_description LONGTEXT DEFAULT NULL,
                CHANGE image ban_image VARCHAR(100) DEFAULT NULL,
                CHANGE email ban_email VARCHAR(50) NOT NULL;
            ALTER TABLE ban_banner ADD CONSTRAINT FRK_ban_bac_id
                FOREIGN KEY (ban_bac_id) REFERENCES bac_banner_customer (bac_id);
            CREATE INDEX IDX_ban_bac_id ON ban_banner (ban_bac_id);
            ALTER TABLE ban_banner ADD PRIMARY KEY (ban_id);

            ALTER TABLE trn_transporter MODIFY vervoerder_id BIGINT NOT NULL;
            ALTER TABLE trn_transporter DROP PRIMARY KEY;
            ALTER TABLE trn_transporter
                DROP prorail_desc,
                CHANGE vervoerder_id trn_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE omschrijving trn_name VARCHAR(35) NOT NULL,
                CHANGE iff_code trn_iff_code INT DEFAULT NULL;
            CREATE UNIQUE INDEX UNQ_trn_name ON trn_transporter (trn_name);
            ALTER TABLE trn_transporter ADD PRIMARY KEY (trn_id);

            ALTER TABLE usp_user_preference MODIFY prefid BIGINT NOT NULL;
            ALTER TABLE usp_user_preference DROP PRIMARY KEY;
            ALTER TABLE usp_user_preference
                CHANGE prefid usp_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE sleutel usp_key VARCHAR(25) NOT NULL,
                CHANGE type usp_type VARCHAR(50) NOT NULL,
                CHANGE description usp_description VARCHAR(90) NOT NULL,
                CHANGE default_value usp_default_value VARCHAR(200) NOT NULL,
                CHANGE volgorde usp_order INT NOT NULL;
            CREATE INDEX IDX_usp_key ON usp_user_preference (usp_key);
            ALTER TABLE usp_user_preference ADD PRIMARY KEY (usp_id);

            ALTER TABLE fpl_forum_post_log MODIFY id BIGINT NOT NULL;
            ALTER TABLE fpl_forum_post_log DROP PRIMARY KEY;
            ALTER TABLE fpl_forum_post_log
                CHANGE id fpl_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE postid fpl_fop_id BIGINT DEFAULT NULL,
                CHANGE actie fpl_action INT DEFAULT 0 NOT NULL;
            ALTER TABLE fpl_forum_post_log ADD CONSTRAINT FRK_fpl_fop_id
                FOREIGN KEY (fpl_fop_id) REFERENCES fop_forum_post (fop_id);
            CREATE INDEX IDX_fpl_fop_id ON fpl_forum_post_log (fpl_fop_id);
            ALTER TABLE fpl_forum_post_log ADD PRIMARY KEY (fpl_id);

            ALTER TABLE ott_official_train_table
                CHANGE ott_transporter_id ott_trn_id BIGINT DEFAULT NULL,
                CHANGE ott_characteristic_id ott_cha_id BIGINT DEFAULT NULL,
                CHANGE ott_route_id ott_rou_id BIGINT DEFAULT NULL,
                CHANGE ott_location_id ott_loc_id BIGINT DEFAULT NULL;
            ALTER TABLE ott_official_train_table ADD CONSTRAINT FRK_ott_trn_id
                FOREIGN KEY (ott_trn_id) REFERENCES trn_transporter (trn_id);
            ALTER TABLE ott_official_train_table ADD CONSTRAINT FRK_ott_cha_id
                FOREIGN KEY (ott_cha_id) REFERENCES cha_characteristic (cha_id);
            ALTER TABLE ott_official_train_table ADD CONSTRAINT FRK_ott_rou_id
                FOREIGN KEY (ott_rou_id) REFERENCES rou_route (rou_id);
            ALTER TABLE ott_official_train_table ADD CONSTRAINT FRK_ott_loc_id
                FOREIGN KEY (ott_loc_id) REFERENCES loc_location (loc_id);
            CREATE INDEX IDX_ott_trn_id ON ott_official_train_table (ott_trn_id);
            CREATE INDEX IDX_ott_cha_id ON ott_official_train_table (ott_cha_id);
            CREATE INDEX IDX_ott_loc_id ON ott_official_train_table (ott_loc_id);
            CREATE INDEX IDX_ott_rou_id ON ott_official_train_table (ott_rou_id);

            ALTER TABLE usc_user_company MODIFY bedrijf_id BIGINT NOT NULL;
            ALTER TABLE usc_user_company DROP PRIMARY KEY;
            ALTER TABLE usc_user_company
                CHANGE bedrijf_id usc_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE naam usc_name VARCHAR(15) NOT NULL;
            ALTER TABLE usc_user_company ADD PRIMARY KEY (usc_id);

            ALTER TABLE ttf_train_table_first_last DROP PRIMARY KEY;
            ALTER TABLE ttf_train_table_first_last
                CHANGE tdr_nr ttf_tty_id BIGINT NOT NULL,
                CHANGE treinid ttf_rou_id BIGINT NOT NULL,
                CHANGE v_locatieid ttf_first_loc_id BIGINT DEFAULT NULL,
                CHANGE a_locatieid ttf_last_loc_id BIGINT DEFAULT NULL,
                CHANGE v_actie ttf_first_action VARCHAR(1) DEFAULT \'-\' NOT NULL,
                CHANGE v_tijd ttf_first_time INT DEFAULT 0 NOT NULL,
                CHANGE a_actie ttf_last_action VARCHAR(1) DEFAULT \'-\' NOT NULL,
                CHANGE a_tijd ttf_last_time INT DEFAULT 0 NOT NULL,
                CHANGE dag ttf_day_number INT DEFAULT 1 NOT NULL;
            ALTER TABLE ttf_train_table_first_last ADD CONSTRAINT FRK_ttf_tty_id
                FOREIGN KEY (ttf_tty_id) REFERENCES tty_train_table_year (tty_id);
            ALTER TABLE ttf_train_table_first_last ADD CONSTRAINT FRK_ttf_rou_id
                FOREIGN KEY (ttf_rou_id) REFERENCES rou_route (rou_id);
            ALTER TABLE ttf_train_table_first_last ADD CONSTRAINT FRK_ttf_first_loc_id
                FOREIGN KEY (ttf_first_loc_id) REFERENCES loc_location (loc_id);
            ALTER TABLE ttf_train_table_first_last ADD CONSTRAINT FRK_ttf_last_loc_id
                FOREIGN KEY (ttf_last_loc_id) REFERENCES loc_location (loc_id);
            CREATE INDEX IDX_ttf_tty_id ON ttf_train_table_first_last (ttf_tty_id);
            CREATE INDEX IDX_ttf_rou_id ON ttf_train_table_first_last (ttf_rou_id);
            CREATE INDEX IDX_ttf_first_loc_id ON ttf_train_table_first_last (ttf_first_loc_id);
            CREATE INDEX IDX_ttf_last_loc_id ON ttf_train_table_first_last (ttf_last_loc_id);
            ALTER TABLE ttf_train_table_first_last ADD PRIMARY KEY (ttf_tty_id, ttf_rou_id, ttf_day_number);

            ALTER TABLE hel_help MODIFY contentid BIGINT NOT NULL;
            ALTER TABLE hel_help DROP PRIMARY KEY;
            ALTER TABLE hel_help
                CHANGE titel hel_title TINYTEXT NOT NULL,
                CHANGE template hel_template TINYTEXT NOT NULL,
                CHANGE contentid hel_id BIGINT AUTO_INCREMENT NOT NULL;
            ALTER TABLE hel_help ADD PRIMARY KEY (hel_id);

            ALTER TABLE sho_shout MODIFY sht_id BIGINT NOT NULL;
            ALTER TABLE sho_shout DROP PRIMARY KEY;
            ALTER TABLE sho_shout
                CHANGE sht_id sho_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE sht_uid sho_use_id BIGINT DEFAULT NULL,
                CHANGE sht_ip sho_ip_address BIGINT NOT NULL,
                CHANGE sht_datumtijd sho_timestamp DATETIME NOT NULL,
                CHANGE sht_text sho_text VARCHAR(255) NOT NULL;
            ALTER TABLE sho_shout ADD CONSTRAINT FRK_sho_use_id
                FOREIGN KEY (sho_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_sho_use_id ON sho_shout (sho_use_id);
            ALTER TABLE sho_shout ADD PRIMARY KEY (sho_id);

            ALTER TABLE trt_train_table MODIFY tdrid BIGINT NOT NULL;
            ALTER TABLE trt_train_table DROP PRIMARY KEY;
            ALTER TABLE trt_train_table
                CHANGE tdr_nr trt_tty_id BIGINT DEFAULT NULL,
                CHANGE treinid trt_rou_id BIGINT DEFAULT NULL,
                CHANGE rijdagenid trt_rod_id BIGINT DEFAULT NULL,
                CHANGE locatieid trt_loc_id BIGINT DEFAULT NULL,
                CHANGE tdrid trt_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE orderid trt_order INT DEFAULT 1 NOT NULL,
                CHANGE actie trt_action VARCHAR(1) DEFAULT \'-\' NOT NULL,
                CHANGE tijd trt_time INT DEFAULT 0 NOT NULL,
                CHANGE spoor trt_track VARCHAR(3) DEFAULT NULL;
            ALTER TABLE trt_train_table ADD CONSTRAINT FRK_trt_tty_id
                FOREIGN KEY (trt_tty_id) REFERENCES tty_train_table_year (tty_id);
            ALTER TABLE trt_train_table ADD CONSTRAINT FRK_trt_rou_id
                FOREIGN KEY (trt_rou_id) REFERENCES rou_route (rou_id);
            ALTER TABLE trt_train_table ADD CONSTRAINT FRK_trt_rod_id
                FOREIGN KEY (trt_rod_id) REFERENCES rod_route_operation_days (rod_id);
            ALTER TABLE trt_train_table ADD CONSTRAINT FRK_trt_loc_id
                FOREIGN KEY (trt_loc_id) REFERENCES loc_location (loc_id);
            CREATE INDEX IDX_trt_tty_id ON trt_train_table (trt_tty_id);
            CREATE INDEX IDX_trt_rod_id ON trt_train_table (trt_rod_id);
            CREATE INDEX IDX_trt_time ON trt_train_table (trt_time);
            CREATE INDEX IDX_trt_loc_id ON trt_train_table (trt_loc_id);
            CREATE INDEX IDX_trt_rou_id ON trt_train_table (trt_rou_id);
            ALTER TABLE trt_train_table ADD PRIMARY KEY (trt_id);

            ALTER TABLE fop_forum_post MODIFY postid BIGINT NOT NULL;
            ALTER TABLE fop_forum_post DROP PRIMARY KEY;
            ALTER TABLE fop_forum_post
                CHANGE authorid fop_author_use_id BIGINT DEFAULT NULL,
                CHANGE discussionid fop_fod_id BIGINT DEFAULT NULL,
                CHANGE edit_uid fop_editor_use_id BIGINT DEFAULT NULL,
                CHANGE wiki_uid fop_wiki_checker_use_id BIGINT DEFAULT NULL,
                CHANGE postid fop_id BIGINT AUTO_INCREMENT NOT NULL,
                CHANGE timestamp fop_timestamp DATETIME NOT NULL,
                CHANGE edit_timestamp fop_edit_timestamp DATETIME DEFAULT NULL,
                CHANGE edit_reason fop_edit_reason VARCHAR(50) DEFAULT NULL,
                CHANGE sign_on fop_signature_on TINYINT(1) NOT NULL;
            ALTER TABLE fop_forum_post ADD CONSTRAINT FRK_fop_author_use_id
                FOREIGN KEY (fop_author_use_id) REFERENCES use_user (use_id);
            ALTER TABLE fop_forum_post ADD CONSTRAINT FRK_fop_fod_id
                FOREIGN KEY (fop_fod_id) REFERENCES fod_forum_discussion (fod_id);
            ALTER TABLE fop_forum_post ADD CONSTRAINT FRK_fop_editor_use_id
                FOREIGN KEY (fop_editor_use_id) REFERENCES use_user (use_id);
            ALTER TABLE fop_forum_post ADD CONSTRAINT FRK_fop_wiki_checker_use_id
                FOREIGN KEY (fop_wiki_checker_use_id) REFERENCES use_user (use_id);
            CREATE INDEX IDX_fop_editor_use_id ON fop_forum_post (fop_editor_use_id);
            CREATE INDEX IDX_fop_wiki_checker_use_id ON fop_forum_post (fop_wiki_checker_use_id);
            CREATE INDEX IDX_fop_timestamp ON fop_forum_post (fop_timestamp);
            CREATE INDEX IDX_fop_author_use_id ON fop_forum_post (fop_author_use_id);
            CREATE INDEX IDX_fop_fod_id ON fop_forum_post (fop_fod_id);
            ALTER TABLE fop_forum_post ADD PRIMARY KEY (fop_id);
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
