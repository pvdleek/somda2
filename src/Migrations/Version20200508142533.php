<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Location;
use App\Entity\LocationCategory;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200508142533 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add trainTable management: remove old blocks, add "unknown" location';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            INSERT INTO `somda_verk` (`afkorting`, `landid`, `description`, `spot_allowed`)
            VALUES (
                \'' . Location::UNKNOWN_NAME . '\',
                ' . LocationCategory::NO_LONGER_VALID_ID . ',
                \'Onbekende locatie\',
                0
            );
        ');

        $this->addSql('DELETE FROM `somda_stats_blokken` WHERE `blokid` = 8');
        $this->addSql('DELETE FROM `somda_blokken` WHERE `blokid` = 8');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
