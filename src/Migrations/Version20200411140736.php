<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200411140736 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Clean-up user preferences';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('
            DELETE FROM `somda_users_prefs`
            WHERE `prefid` IN (
                SELECT `prefid` FROM `somda_prefs` WHERE `sleutel` IN (
                    \'intro_text\', \'omloop_tabel\', \'tdr_tabel\', \'forum_new_old\', \'mat_mijnspots\',
                    \'mat_recentespots\', \'mat_spotsinvoer\', \'spotting_last\', \'myspots_default_nr\',
                    \'myspots_default_start\', \'myspots_sort\', \'myspots_sort_method\', \'myspots_sort2\',
                    \'myspots_sort_method2\'
                )
            )
        ');
        $this->addSql('
            DELETE FROM `somda_prefs` WHERE `sleutel` IN (
                \'intro_text\', \'omloop_tabel\', \'tdr_tabel\', \'forum_new_old\', \'mat_mijnspots\',
                \'mat_recentespots\', \'mat_spotsinvoer\', \'spotting_last\', \'myspots_default_nr\',
                \'myspots_default_start\', \'myspots_sort\', \'myspots_sort_method\', \'myspots_sort2\',
                \'myspots_sort_method2\'
            )
        ');

        $this->addSql('UPDATE `somda_prefs` SET `type` = \'table|location|name|description\' WHERE `prefid` = 8');
        $this->addSql('
            UPDATE `somda_prefs`
            SET `default_value` = \';welcome;dashboard;forum;spots;foutespots;drgl;werkzaamheden;' .
                'shortcuts;spoornieuws;doorkomst;weer;spots;news;\'
            WHERE `prefid` = 19');
        $this->addSql('
            UPDATE `somda_users_prefs`
            SET `value` = \';welcome;dashboard;forum;spots;foutespots;drgl;werkzaamheden;' .
                'shortcuts;spoornieuws;doorkomst;weer;spots;news;\'
            WHERE `prefid` = 19');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
