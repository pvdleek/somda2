<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200518100402 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add help-function';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DELETE FROM `somda_help` WHERE `contentid` = 0');
        $this->addSql('
            ALTER TABLE `somda_help`
            ADD `template` TINYTEXT NOT NULL,
            DROP `url`,
            DROP `authorid`,
            CHANGE `contentid` `contentid` BIGINT AUTO_INCREMENT NOT NULL,
            CHANGE `titel` `titel` TINYTEXT NOT NULL
        ');

        $this->addSql('UPDATE `somda_help` SET `template` = \'introduction\' WHERE `contentid` = 1');
        $this->addSql('UPDATE `somda_help` SET `template` = \'security\' WHERE `contentid` = 10');
        $this->addSql('UPDATE `somda_help` SET `template` = \'login\' WHERE `contentid` = 400');
        $this->addSql('UPDATE `somda_help` SET `template` = \'locations\' WHERE `contentid` = 600');
        $this->addSql('UPDATE `somda_help` SET `template` = \'passingRoutes\' WHERE `contentid` = 1200');
        $this->addSql('UPDATE `somda_help` SET `template` = \'trainCompositions\' WHERE `contentid` = 1400');
        $this->addSql('UPDATE `somda_help` SET `template` = \'spotInput\' WHERE `contentid` = 1700');
        $this->addSql('UPDATE `somda_help` SET `template` = \'news\' WHERE `contentid` = 2000');
        $this->addSql('UPDATE `somda_help` SET `template` = \'settings\' WHERE `contentid` = 3500');
        $this->addSql('UPDATE `somda_help` SET `template` = \'mySpots\' WHERE `contentid` = 3800');
        $this->addSql('UPDATE `somda_help` SET `template` = \'uic\' WHERE `contentid` = 3900');
        $this->addSql('UPDATE `somda_help` SET `template` = \'statistics\' WHERE `contentid` = 5500');
        $this->addSql('UPDATE `somda_help` SET `template` = \'firstTime\' WHERE `contentid` = 100000');
        $this->addSql('UPDATE `somda_help` SET `template` = \'faq\' WHERE `contentid` = 110000');

        $this->addSql('
            DELETE FROM `somda_help` WHERE `contentid` IN (
                500, 900, 1000, 2300, 2400, 2500, 2600, 2700, 3400, 4300, 4400, 4800, 5200, 5700, 120000
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
