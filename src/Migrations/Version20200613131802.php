<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200613131802 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add unread stuff';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE `somda_blokken` SET `route` = \'forum_unread\' WHERE `blokid` = 47');
        $this->addSql('DELETE FROM `somda_stats_blokken` WHERE `blokid` = 31');
        $this->addSql('DELETE FROM `somda_blokken` WHERE `blokid` = 31');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
