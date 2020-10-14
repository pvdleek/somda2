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
        $this->addSql('DROP TABLE `somda_api_logging`');
        $this->addSql('DROP TABLE `somda_don_donatie`');

        $this->addSql('RENAME TABLE `somda_banner` TO `ban_banner`,
             `somda_banner_customer` TO `bac_banner_customer`,
             `somda_banner_customer_user` TO `bcu_banner_customer_user`,
             `somda_banner_hits` TO `bah_banner_hit`,
             `somda_banner_views` TO `bav_banner_views`,
             `somda_blokken` TO `blo_block`,
             `somda_ddar` TO `dda_ddar`,
             `somda_drgl` TO `spr_special_route`,
             `somda_drgl_read` TO `spr_special_route`,
             `` TO ``,
             `` TO ``,
             `somda_forum_cats` TO `foc_forum_category`,
             `somda_forum_discussion` TO `fod_forum_discussion`,
             `` TO ``,
             `somda_forum_mods` TO `ffm_forum_forum_moderator`,
             `somda_forum_posts` TO `fop_forum_post`,
             `` TO ``,
             `` TO ``,
             `somda_help_text` TO `blh_block_help`,
             `` TO ``,
             `` TO ``,
             `` TO ``,
             `somda_karakteristiek` TO `cha_characteristic`,
             `` TO ``,
             `` TO ``,
             `` TO ``,
             `somda_users` TO `use_user`,
             `` TO ``,
             `` TO ``,
             `` TO ``,
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
