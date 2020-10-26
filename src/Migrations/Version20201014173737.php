<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201014173737 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Migrate table-names to correct structure - drop all indices and foreign keys';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            ALTER TABLE bah_banner_hit DROP FOREIGN KEY FK_8610F3216BBC5658;
            
            ALTER TABLE ner_news_read DROP FOREIGN KEY FK_AF652C5539B0606;
            ALTER TABLE ner_news_read DROP FOREIGN KEY FK_AF652C58C510C37;
            DROP INDEX IDX_AF652C58C510C37 ON ner_news_read;
            DROP INDEX IDX_AF652C5539B0606 ON ner_news_read;

            ALTER TABLE fpn_forum_post_alert_note DROP FOREIGN KEY FK_502511CE3412DD5F;
            ALTER TABLE fpn_forum_post_alert_note DROP FOREIGN KEY FK_502511CEF2677207;
            DROP INDEX IDX_502511CE3412DD5F ON fpn_forum_post_alert_note;
            DROP INDEX idx_47898_alertid ON fpn_forum_post_alert_note;

            ALTER TABLE rnf_rail_news_source_feed DROP FOREIGN KEY FK_8A257AA6AD7A950;
            DROP INDEX IDX_8A257AA6AD7A950 ON rnf_rail_news_source_feed;

            ALTER TABLE ran_rail_news DROP FOREIGN KEY FRK_sns_snb_id;
            DROP INDEX FRK_sns_snb_id ON ran_rail_news;

            ALTER TABLE pov_poll_vote DROP FOREIGN KEY FK_75CFE387539B0606;
            ALTER TABLE pov_poll_vote DROP FOREIGN KEY FK_75CFE3876F5F43AE;
            DROP INDEX IDX_75CFE3876F5F43AE ON pov_poll_vote;
            DROP INDEX IDX_75CFE387539B0606 ON pov_poll_vote;

            DROP INDEX idx_48102_omschrijving ON cha_characteristic;

            ALTER TABLE fsl_forum_search_list DROP FOREIGN KEY FK_C9D9A41E7510F6AF;
            ALTER TABLE fsl_forum_search_list DROP FOREIGN KEY FK_C9D9A41EE9BD09BA;
            DROP INDEX IDX_C9D9A41EE9BD09BA ON fsl_forum_search_list;
            DROP INDEX IDX_C9D9A41E7510F6AF ON fsl_forum_search_list;
            
            ALTER TABLE pot_point_of_interest_text DROP FOREIGN KEY FK_91652C16FD33F6CE;

            ALTER TABLE usg_user_group DROP FOREIGN KEY FK_B2ACF076539B0606;
            ALTER TABLE usg_user_group DROP FOREIGN KEY FK_B2ACF0767805AC12;
            DROP INDEX IDX_B2ACF0767805AC12 ON usg_user_group;
            DROP INDEX IDX_B2ACF076539B0606 ON usg_user_group;

            DROP INDEX idx_49076_active ON use_user;
            DROP INDEX idx_49053_uname ON use_user;

            ALTER TABLE upf_user_preference_value DROP FOREIGN KEY FK_3920F080539B0606;
            ALTER TABLE upf_user_preference_value DROP FOREIGN KEY FK_3920F08087B0DAC1;
            DROP INDEX IDX_3920F080539B0606 ON upf_user_preference_value;
            DROP INDEX IDX_3920F08087B0DAC1 ON upf_user_preference_value;

            ALTER TABLE blo_block DROP FOREIGN KEY FK_B4865B064F2A0381;
            DROP INDEX FK_B4865B064F2A0381 ON blo_block;

            ALTER TABLE fdw_forum_discussion_wiki DROP FOREIGN KEY FK_D487B7F1FCC0F19E;
            DROP INDEX idx_47927_discussionid ON fdw_forum_discussion_wiki;

            DROP INDEX idx_49046_treinnr ON rou_route;

            ALTER TABLE rot_route_train DROP FOREIGN KEY FK_C2BF79AA68F454BD;
            ALTER TABLE rot_route_train DROP FOREIGN KEY FK_C2BF79AAAE60685A;
            ALTER TABLE rot_route_train DROP FOREIGN KEY FK_C2BF79AAE14DDC7E;
            ALTER TABLE rot_route_train DROP FOREIGN KEY FK_C2BF79AAF4E25321;
            DROP INDEX IDX_C2BF79AAAE60685A ON rot_route_train;
            DROP INDEX IDX_C2BF79AAF4E25321 ON rot_route_train;
            DROP INDEX IDX_C2BF79AA68F454BD ON rot_route_train;
            DROP INDEX IDX_C2BF79AAE14DDC7E ON rot_route_train;

            DROP INDEX idx_48191_date ON pol_poll;

            ALTER TABLE loc_location DROP FOREIGN KEY FK_F7F314CE61A61C;
            DROP INDEX idx_49103_landid ON loc_location;
            DROP INDEX idx_49103_description ON loc_location;
            DROP INDEX idx_49103_afkorting_2 ON loc_location;

            ALTER TABLE fpf_forum_post_favorite DROP FOREIGN KEY FRK_fpf_postid;
            ALTER TABLE fpf_forum_post_favorite DROP FOREIGN KEY FRK_fpf_uid;
            DROP INDEX IDX_AA766AFC7510F6AF ON fpf_forum_post_favorite;
            DROP INDEX IDX_AA766AFC539B0606 ON fpf_forum_post_favorite;

            ALTER TABLE spo_spot DROP FOREIGN KEY FK_11A6C5C8539B0606;
            ALTER TABLE spo_spot DROP FOREIGN KEY FK_11A6C5C868F454BD;
            ALTER TABLE spo_spot DROP FOREIGN KEY FK_11A6C5C8890261A4;
            ALTER TABLE spo_spot DROP FOREIGN KEY FK_11A6C5C8D6E3DC6C;
            ALTER TABLE spo_spot DROP FOREIGN KEY FK_11A6C5C8F4E25321;
            DROP INDEX idx_48259_matid ON spo_spot;
            DROP INDEX IDX_11A6C5C868F454BD ON spo_spot;
            DROP INDEX IDX_11A6C5C8D6E3DC6C ON spo_spot;
            DROP INDEX idx_48259_datum ON spo_spot;
            DROP INDEX idx_48259_uid ON spo_spot;
            DROP INDEX IDX_11A6C5C8F4E25321 ON spo_spot;
            DROP INDEX idx_48259_treinid ON spo_spot;

            ALTER TABLE tra_train DROP FOREIGN KEY FK_355CF7922A00C2;
            ALTER TABLE tra_train DROP FOREIGN KEY FK_355CF79F734A20F;
            DROP INDEX idx_48117_vervoerder_id ON tra_train;
            DROP INDEX FK_355CF79F734A20F ON tra_train;
            DROP INDEX idx_48117_nummer ON tra_train;

            DROP INDEX idx_48035_woord ON fsw_forum_search_word;

            ALTER TABLE dda_ddar DROP FOREIGN KEY FK_9A508BF890261A4;
            ALTER TABLE dda_ddar DROP FOREIGN KEY FK_9A508BFC65F5051;
            DROP INDEX IDX_9A508BFC65F5051 ON dda_ddar;
            DROP INDEX idx_47846_matid ON dda_ddar;

            ALTER TABLE rll_route_list_location DROP FOREIGN KEY FK_1A52615B9CBF59B5;
            ALTER TABLE rll_route_list_location DROP FOREIGN KEY FK_1A52615BAE60685A;
            ALTER TABLE rll_route_list_location DROP FOREIGN KEY FK_1A52615BD6E3DC6C;
            DROP INDEX IDX_1A52615BD6E3DC6C ON rll_route_list_location;
            DROP INDEX IDX_1A52615BAE60685A ON rll_route_list_location;
            DROP INDEX IDX_1A52615B9CBF59B5 ON rll_route_list_location;

            ALTER TABLE foa_forum_favorite DROP FOREIGN KEY FK_4E8B7C93539B0606;
            ALTER TABLE foa_forum_favorite DROP FOREIGN KEY FK_4E8B7C93FCC0F19E;
            DROP INDEX IDX_4E8B7C93FCC0F19E ON foa_forum_favorite;
            DROP INDEX IDX_4E8B7C93539B0606 ON foa_forum_favorite;

            ALTER TABLE blh_block_help DROP FOREIGN KEY FK_397D7775711B2385;

            DROP INDEX idx_date ON sta_statistic;

            ALTER TABLE fpt_forum_post_text DROP FOREIGN KEY FK_25A0B80F7510F6AF;

            ALTER TABLE poi_point_of_interest DROP FOREIGN KEY FK_6164DED2C0A1CCC;
            ALTER TABLE poi_point_of_interest DROP FOREIGN KEY FK_6164DED8A6CCEB4;
            ALTER TABLE poi_point_of_interest DROP FOREIGN KEY FK_6164DEDB5034D76;
            ALTER TABLE poi_point_of_interest DROP FOREIGN KEY FK_6164DEDE2D85197;
            ALTER TABLE poi_point_of_interest DROP FOREIGN KEY FK_6164DEDFA5300E5;
            DROP INDEX IDX_6164DED2C0A1CCC ON poi_point_of_interest;
            DROP INDEX IDX_6164DEDE2D85197 ON poi_point_of_interest;
            DROP INDEX IDX_6164DEDB5034D76 ON poi_point_of_interest;
            DROP INDEX IDX_6164DED8A6CCEB4 ON poi_point_of_interest;
            DROP INDEX IDX_6164DEDFA5300E5 ON poi_point_of_interest;

            ALTER TABLE trc_train_composition DROP FOREIGN KEY FK_2FC3E54DE70B032;
            DROP INDEX idx_48145_typeid ON trc_train_composition;

            ALTER TABLE srr_special_route_read DROP FOREIGN KEY FK_7CF8CCE9539B0606;
            ALTER TABLE srr_special_route_read DROP FOREIGN KEY FK_7CF8CCE9B869D711;
            DROP INDEX IDX_7CF8CCE9B869D711 ON srr_special_route_read;
            DROP INDEX IDX_7CF8CCE9539B0606 ON srr_special_route_read;

            ALTER TABLE rol_route_list DROP FOREIGN KEY FK_D7A6066022A00C2;
            ALTER TABLE rol_route_list DROP FOREIGN KEY FK_D7A60660AE60685A;
            ALTER TABLE rol_route_list DROP FOREIGN KEY FK_D7A60660FBDE844F;
            DROP INDEX idx_48442_nr_start ON rol_route_list;
            DROP INDEX IDX_D7A6066022A00C2 ON rol_route_list;
            DROP INDEX IDX_D7A60660AE60685A ON rol_route_list;
            DROP INDEX IDX_D7A60660FBDE844F ON rol_route_list;
            DROP INDEX idx_48442_nr_eind ON rol_route_list;

            ALTER TABLE rlr_route_list_route DROP FOREIGN KEY FK_95ACCAE668F454BD;
            ALTER TABLE rlr_route_list_route DROP FOREIGN KEY FK_95ACCAE69CBF59B5;
            DROP INDEX IDX_95ACCAE69CBF59B5 ON rlr_route_list_route;
            DROP INDEX IDX_95ACCAE668F454BD ON rlr_route_list_route;

            ALTER TABLE fod_forum_discussion DROP FOREIGN KEY FK_64C2DF7E3412DD5F;
            ALTER TABLE fod_forum_discussion DROP FOREIGN KEY FK_64C2DF7EEDB4D5F3;
            DROP INDEX IDX_64C2DF7E3412DD5F ON fod_forum_discussion;
            DROP INDEX idx_47915_forumid ON fod_forum_discussion;

            ALTER TABLE fof_forum_forum DROP FOREIGN KEY FK_ABD72EFF3632DFC5;
            DROP INDEX idx_47937_catid ON fof_forum_forum;

            ALTER TABLE ffm_forum_forum_moderator DROP FOREIGN KEY FK_E20AB6A4539B0606;
            ALTER TABLE ffm_forum_forum_moderator DROP FOREIGN KEY FK_E20AB6A4EDB4D5F3;
            DROP INDEX IDX_E20AB6A4EDB4D5F3 ON ffm_forum_forum_moderator;
            DROP INDEX IDX_E20AB6A4539B0606 ON ffm_forum_forum_moderator;

            ALTER TABLE log_log DROP FOREIGN KEY FK_8127138D539B0606;
            DROP INDEX IDX_8127138D539B0606 ON log_log;

            ALTER TABLE bcu_banner_customer_user DROP FOREIGN KEY FK_C9A88E10539B0606;
            ALTER TABLE bcu_banner_customer_user DROP FOREIGN KEY FK_C9A88E10BF396750;
            DROP INDEX IDX_C9A88E10BF396750 ON bcu_banner_customer_user;
            DROP INDEX IDX_C9A88E10539B0606 ON bcu_banner_customer_user;

            DROP INDEX idx_48139_volgorde ON tnp_train_name_pattern;

            ALTER TABLE tcp_train_composition_proposition DROP FOREIGN KEY FK_C6C1DF0D4CD774E2;
            ALTER TABLE tcp_train_composition_proposition DROP FOREIGN KEY FK_C6C1DF0D539B0606;
            DROP INDEX IDX_C6C1DF0D4CD774E2 ON tcp_train_composition_proposition;
            DROP INDEX IDX_C6C1DF0D539B0606 ON tcp_train_composition_proposition;

            ALTER TABLE bav_banner_view DROP FOREIGN KEY FK_F1B9EA066BBC5658;
            DROP INDEX idx_47831_bannerid ON bav_banner_view;

            ALTER TABLE spe_spot_extra DROP FOREIGN KEY FK_6EAD9515BFB6C75;

            ALTER TABLE fpa_forum_post_alert DROP FOREIGN KEY FK_A2F3B42C65525B5F;
            ALTER TABLE fpa_forum_post_alert DROP FOREIGN KEY FK_A2F3B42C7510F6AF;
            DROP INDEX IDX_A2F3B42C65525B5F ON fpa_forum_post_alert;
            DROP INDEX idx_47886_postid ON fpa_forum_post_alert;

            ALTER TABLE stb_statistic_block DROP FOREIGN KEY FK_7FAF7B1A711B2385;
            DROP INDEX IDX_7FAF7B1A711B2385 ON stb_statistic_block;

            ALTER TABLE usi_user_info DROP FOREIGN KEY FK_46F59BD0539B0606;
            ALTER TABLE usi_user_info DROP FOREIGN KEY FK_46F59BD0740E9210;
            DROP INDEX IDX_46F59BD0740E9210 ON usi_user_info;
            DROP INDEX idx_49074_gebdatum ON usi_user_info;

            ALTER TABLE ban_banner DROP FOREIGN KEY FK_D93888C264FBF307;
            DROP INDEX IDX_D93888C264FBF307 ON ban_banner;

            DROP INDEX idx_49122_omschrijving ON trn_transporter;

            DROP INDEX idx_48215_sleutel ON usp_user_preference;

            ALTER TABLE fpl_forum_post_log DROP FOREIGN KEY FK_256DFB117510F6AF;
            DROP INDEX IDX_256DFB117510F6AF ON fpl_forum_post_log;

            ALTER TABLE ott_official_train_table DROP FOREIGN KEY FK_ott_characteristic_id;
            ALTER TABLE ott_official_train_table DROP FOREIGN KEY FK_ott_location_id;
            ALTER TABLE ott_official_train_table DROP FOREIGN KEY FK_ott_route_id;
            ALTER TABLE ott_official_train_table DROP FOREIGN KEY FK_ott_transporter_id;
            DROP INDEX idx_ott_route_id ON ott_official_train_table;
            DROP INDEX IDX_4577F52E2EDDB7B4 ON ott_official_train_table;
            DROP INDEX IDX_4577F52EBE696BF ON ott_official_train_table;
            DROP INDEX idx_ott_location_id ON ott_official_train_table;

            ALTER TABLE ttf_train_table_first_last DROP FOREIGN KEY FK_1BACB9635E53C5B;
            ALTER TABLE ttf_train_table_first_last DROP FOREIGN KEY FK_1BACB96368F454BD;
            ALTER TABLE ttf_train_table_first_last DROP FOREIGN KEY FK_1BACB9638228ED13;
            ALTER TABLE ttf_train_table_first_last DROP FOREIGN KEY FK_1BACB963AE60685A;
            DROP INDEX IDX_1BACB96368F454BD ON ttf_train_table_first_last;
            DROP INDEX IDX_1BACB9638228ED13 ON ttf_train_table_first_last;
            DROP INDEX IDX_1BACB963AE60685A ON ttf_train_table_first_last;
            DROP INDEX IDX_1BACB9635E53C5B ON ttf_train_table_first_last;

            ALTER TABLE sho_shout DROP FOREIGN KEY FK_88E10AFB97AD1E10;
            DROP INDEX IDX_88E10AFB97AD1E10 ON sho_shout;

            ALTER TABLE trt_train_table DROP FOREIGN KEY FK_84B606F668F454BD;
            ALTER TABLE trt_train_table DROP FOREIGN KEY FK_84B606F687CF3DBF;
            ALTER TABLE trt_train_table DROP FOREIGN KEY FK_84B606F6AE60685A;
            ALTER TABLE trt_train_table DROP FOREIGN KEY FK_84B606F6D6E3DC6C;
            DROP INDEX idx_48320_tijd ON trt_train_table;
            DROP INDEX IDX_84B606F6AE60685A ON trt_train_table;
            DROP INDEX idx_48320_treinid ON trt_train_table;
            DROP INDEX IDX_84B606F687CF3DBF ON trt_train_table;
            DROP INDEX idx_48320_locatieid ON trt_train_table;

            ALTER TABLE fop_forum_post DROP FOREIGN KEY FK_40FD24D143C6493;
            ALTER TABLE fop_forum_post DROP FOREIGN KEY FK_40FD24D3412DD5F;
            ALTER TABLE fop_forum_post DROP FOREIGN KEY FK_40FD24D9ECDC13D;
            ALTER TABLE fop_forum_post DROP FOREIGN KEY FK_40FD24DFCC0F19E;
            DROP INDEX idx_47961_discussionid ON fop_forum_post;
            DROP INDEX IDX_40FD24D143C6493 ON fop_forum_post;
            DROP INDEX idx_47961_authorid ON fop_forum_post;
            DROP INDEX IDX_40FD24D9ECDC13D ON fop_forum_post;
            DROP INDEX idx_47961_timestamp ON fop_forum_post;
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
