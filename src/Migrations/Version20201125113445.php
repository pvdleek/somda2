<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Entity\UserPreference;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201125113445 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add app-settings to the user-preferences';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            INSERT INTO `somda_prefs` (`sleutel`, `type`, `description`, `default_value`, `volgorde`)
            VALUES (
                \'' . UserPreference::KEY_APP_MARK_FORUM_READ . '\',
                \'boolean\',
                \'Markeer forum-discussies die ik bekijk als gelezen\',
                \'0\',
                510
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
