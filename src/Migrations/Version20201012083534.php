<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201012083534 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Migrate table-names to correct structure';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE IF EXISTS `somda_api_logging`');
        $this->addSql('DROP TABLE IF EXISTS `somda_don_donatie`');
        $this->addSql('DROP TABLE IF EXISTS `somda_rechten`');
        $this->addSql('DROP TABLE IF EXISTS `somda_tdr_in`');
        $this->addSql('DROP TABLE IF EXISTS `somda_tdr_in_s_e`');

        $this->addSql('RENAME TABLE
             `somda_banner` TO `ban_banner`,
             `somda_banner_customer` TO `bac_banner_customer`,
             `somda_banner_customer_user` TO `bcu_banner_customer_user`,
             `somda_banner_hits` TO `bah_banner_hit`,
             `somda_banner_views` TO `bav_banner_view`,
             `somda_blokken` TO `blo_block`,
             `somda_ddar` TO `dda_ddar`,
             `somda_drgl` TO `spr_special_route`,
             `somda_drgl_read` TO `srr_special_route_read`,
             `somda_forum_alerts` TO `fpa_forum_post_alert`,
             `somda_forum_alerts_notes` TO `fpn_forum_post_alert_note`,
             `somda_forum_cats` TO `foc_forum_category`,
             `somda_forum_discussion` TO `fod_forum_discussion`,
             `somda_forum_discussion_wiki` TO `fdw_forum_discussion_wiki`,
             `somda_forum_favorites` TO `foa_forum_favorite`,
             `somda_forum_forums` TO `fof_forum_forum`,
             `somda_forum_log` TO `fpl_forum_post_log`,
             `somda_forum_mods` TO `ffm_forum_forum_moderator`,
             `somda_forum_posts` TO `fop_forum_post`,
             `somda_forum_posts_text` TO `fpt_forum_post_text`,
             `somda_forum_read_0` TO `fr0_forum_read_0`,
             `somda_forum_read_1` TO `fr1_forum_read_1`,
             `somda_forum_read_2` TO `fr2_forum_read_2`,
             `somda_forum_read_3` TO `fr3_forum_read_3`,
             `somda_forum_read_4` TO `fr4_forum_read_4`,
             `somda_forum_read_5` TO `fr5_forum_read_5`,
             `somda_forum_read_6` TO `fr6_forum_read_6`,
             `somda_forum_read_7` TO `fr7_forum_read_7`,
             `somda_forum_read_8` TO `fr8_forum_read_8`,
             `somda_forum_read_9` TO `fr9_forum_read_9`,
             `somda_forum_zoeken_lijst` TO `fsl_forum_search_list`,
             `somda_forum_zoeken_woorden` TO `fsw_forum_search_word`,
             `somda_groups` TO `gro_group`,
             `somda_help` TO `hel_help`,
             `somda_help_text` TO `blh_block_help`,
             `somda_jargon` TO `jar_jargon`,
             `somda_karakteristiek` TO `cha_characteristic`,
             `somda_logging` TO `log_log`,
             `somda_mat` TO `tra_train`,
             `somda_mat_changes` TO `tcp_train_composition_proposition`,
             `somda_mat_patterns` TO `tnp_train_name_pattern`,
             `somda_mat_sms` TO `trc_train_composition`,
             `somda_mat_types` TO `tct_train_composition_type`,
             `somda_news` TO `new_news`,
             `somda_news_read` TO `ner_news_read`,
             `somda_poll` TO `pol_poll`,
             `somda_poll_votes` TO `pov_poll_vote`,
             `somda_positie` TO `pos_position`,
             `somda_prefs` TO `usp_user_preference`,
             `somda_rijdagen` TO `rod_route_operation_days`,
             `somda_session` TO `ses_session`,
             `somda_sht_shout` TO `sho_shout`,
             `somda_snb_spoor_nieuws_bron` TO `rns_rail_news_source`,
             `somda_snf_spoor_nieuws_bron_feed` TO `rnf_rail_news_source_feed`,
             `somda_sns_spoor_nieuws` TO `ran_rail_news`,
             `somda_spots` TO `spo_spot`,
             `somda_spots_extra` TO `spe_spot_extra`,
             `somda_spot_provincie` TO `pon_point_of_interest_category`,
             `somda_spot_punt` TO `poi_point_of_interest`,
             `somda_spot_punt_text` TO `pot_point_of_interest_text`,
             `somda_stats` TO `sta_statistic`,
             `somda_stats_blokken` TO `stb_statistic_block`,
             `somda_tdr` TO `trt_train_table`,
             `somda_tdr_drgl` TO `tty_train_table_year`,
             `somda_tdr_route` TO `rll_route_list_location`,
             `somda_tdr_s_e` TO `ttf_train_table_first_last`,
             `somda_tdr_treinnummerlijst` TO `rol_route_list`,
             `somda_tdr_trein_mat` TO `rot_route_train`,
             `somda_tdr_trein_treinnummerlijst` TO `rlr_route_list_route`,
             `somda_trein` TO `rou_route`,
             `somda_users` TO `use_user`,
             `somda_users_companies` TO `usc_user_company`,
             `somda_users_groups` TO `usg_user_group`,
             `somda_users_info` TO `usi_user_info`,
             `somda_users_prefs` TO `upf_user_preference_value`,
             `somda_verk` TO `loc_location`,
             `somda_verk_cats` TO `loa_location_category`,
             `somda_vervoerder` TO `trn_transporter`
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
