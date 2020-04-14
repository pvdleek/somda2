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
    public function getDescription() : string
    {
        return 'Clean-up non-used user preferences';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            DELETE FROM `somda_users_prefs`
            WHERE `prefid` IN (
                SELECT `prefid` FROM `somda_prefs` WHERE `sleutel` IN (
                    \'intro_text\', \'omloop_tabel\', \'tdr_tabel\', \'forum_new_old\', \'mat_mijnspots\',
                    \'mat_recentespots\', \'mat_spotsinvoer\', \'spotting_last\'
                )
            )
        ');
        $this->addSql('
            DELETE FROM `somda_prefs` WHERE `sleutel` IN (
                \'intro_text\', \'omloop_tabel\', \'tdr_tabel\', \'forum_new_old\', \'mat_mijnspots\',
                \'mat_recentespots\', \'mat_spotsinvoer\', \'spotting_last\'
            )
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
