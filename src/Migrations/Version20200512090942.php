<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200512090942 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Clean-up roles';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            INSERT IGNORE INTO `somda_user_groups` (`uid`, `groupid`)
            SELECT `uid`, 9 FROM `somda_user_groups` WHERE `groupid` = 10
        ');
        $this->addSql('DELETE FROM `somda_user_groups` WHERE `groupid` = 10');
        $this->addSql('DELETE FROM `somda_groups` WHERE `groupid` = 10');

        $this->addSql('
            UPDATE `somda_groups` SET
            `name` = \'TDR beheer\',
            `roles` = \'a:2:{i:0;s:28:"ROLE_ADMIN_ROUTE_NUMBER_LIST";i:1;s:26:"ROLE_ADMIN_TRAINTABLE_EDIT";}\'
            WHERE `groupid` = 9
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
