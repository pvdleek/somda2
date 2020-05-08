<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200423152304 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Fix default values in banner table';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE `somda_banner`
            CHANGE `max_views` `max_views` BIGINT DEFAULT 0 NOT NULL,
            CHANGE `max_hits` `max_hits` BIGINT DEFAULT 0 NOT NULL
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
