<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200410103202 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add new-style field to forum-posts';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_forum_posts_text` ADD `new_style` TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql(
            'ALTER TABLE `somda_forum_posts_text` CHANGE `new_style` `new_style` TINYINT(1) DEFAULT \'1\' NOT NULL'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
