<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200929084750 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add forum-post favorites';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE `fpf_forum_post_favorite` (
                `postid` BIGINT NOT NULL,
                `uid` BIGINT NOT NULL,
                INDEX `IDX_AA766AFC7510F6AF` (`postid`),
                INDEX `IDX_AA766AFC539B0606` (`uid`),
                PRIMARY KEY (`postid`, `uid`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE `fpf_forum_post_favorite` ADD CONSTRAINT `FRK_fpf_postid`
            FOREIGN KEY (`postid`) REFERENCES `somda_forum_posts` (`postid`)
        ');
        $this->addSql('
            ALTER TABLE `fpf_forum_post_favorite` ADD CONSTRAINT `FRK_fpf_uid`
            FOREIGN KEY (`uid`) REFERENCES `somda_users` (`uid`)
        ');
    }

    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
