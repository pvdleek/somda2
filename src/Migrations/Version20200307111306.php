<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111306 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Make the menu-parent a foreign key to the table itself in somda_blokken';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // Do some cleanup
        $this->addSql('DELETE FROM `somda_blokken` WHERE `blokid` IN (0, 1, 2, 3)');

        $this->addSql('ALTER TABLE `somda_blokken` ADD COLUMN `parent_block` BIGINT(20) DEFAULT NULL');
        $this->addSql('
            UPDATE `somda_blokken` `b1`
            JOIN `somda_blokken` `b2` ON `b2`.`blokid` = `b1`.`menu_parent`
            SET `b1`.`parent_block` = `b2`.`blokid`
        ');
        $this->addSql('
            ALTER TABLE `somda_blokken` ADD CONSTRAINT `FRK_parent_block`
            FOREIGN KEY (`parent_block`) REFERENCES `somda_blokken`(`blokid`)
        ');
        $this->addSql('ALTER TABLE `somda_blokken` DROP COLUMN `menu_parent`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
