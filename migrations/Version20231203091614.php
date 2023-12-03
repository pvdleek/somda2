<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231203091614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add single table to store last-read post for forum discussions';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS `somda_forum_last_read` (
                `uid` INT(10) UNSIGNED NOT NULL,
                `discussionid` INT(10) UNSIGNED NOT NULL,
                `postid` BIGINT(20) UNSIGNED NOT NULL,
                PRIMARY KEY (`uid`,`discussionid`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE IF EXISTS `somda_forum_last_read`');
    }
}
