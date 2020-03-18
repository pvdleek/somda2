<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111307 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Cleanup the forum';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE somda_forum_posts
            ADD COLUMN `timestamp` TIMESTAMP NULL DEFAULT NULL, ADD COLUMN `edit_timestamp` TIMESTAMP NULL DEFAULT NULL
        ');
        $this->addSql('UPDATE somda_forum_posts SET `timestamp` = CONCAT(date, \' \', time)');
        $this->addSql('
            UPDATE somda_forum_posts
            SET `edit_timestamp` = CONCAT(edit_date, \' \', edit_time)
            WHERE edit_date IS NOT NULL
        ');
        $this->addSql('ALTER TABLE somda_forum_posts CHANGE COLUMN `timestamp` `timestamp` TIMESTAMP NOT NULL');
        $this->addSql('
            ALTER TABLE somda_forum_posts
            DROP COLUMN date, DROP COLUMN time, DROP COLUMN edit_date, DROP COLUMN edit_time
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
