<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200412205929 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add a primary key to the statistics table';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            ALTER TABLE `somda_stats`
            ADD `id` BIGINT AUTO_INCREMENT NOT NULL,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY (`id`)
        ');
        $this->addSql('CREATE UNIQUE INDEX `idx_date` ON `somda_stats` (`datum`)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
