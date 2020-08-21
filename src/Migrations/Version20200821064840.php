<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Entity\UserPreference;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200821064840 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add preference to view the forum posts from new to old';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            INSERT INTO `somda_prefs` (`sleutel`, `type`, `description`, `default_value`, `volgorde`)
            VALUES (
                \'' . UserPreference::KEY_FORUM_NEW_TO_OLD  . '\',
                \'boolean\',
                \'Bekijk de forumberichten van nieuw naar oud\',
                \'0\',
                120
            )
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('
            DELETE FROM `somda_users_prefs` WHERE `prefid` = (
                SELECT `prefid` FROM `somda_prefs` WHERE `sleutel` = \'' . UserPreference::KEY_FORUM_NEW_TO_OLD  . '\'
            )
        ');
        $this->addSql('DELETE FROM `somda_prefs` WHERE `sleutel` = \'' . UserPreference::KEY_FORUM_NEW_TO_OLD  . '\'');
    }
}
