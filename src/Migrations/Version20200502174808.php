<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200502174808 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add code to location-categories';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_verk_cats` ADD `code` VARCHAR(5) NOT NULL');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'NL\' WHERE `verk_catid` = 1');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'D\' WHERE `verk_catid` = 2');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'B\' WHERE `verk_catid` = 3');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'GB\' WHERE `verk_catid` = 4');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'F\' WHERE `verk_catid` = 5');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'CH\' WHERE `verk_catid` = 6');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'I\' WHERE `verk_catid` = 7');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'DK\' WHERE `verk_catid` = 8');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'A\' WHERE `verk_catid` = 9');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'L\' WHERE `verk_catid` = 10');
        $this->addSql('UPDATE `somda_verk_cats` SET `code` = \'PL\' WHERE `verk_catid` = 11');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
