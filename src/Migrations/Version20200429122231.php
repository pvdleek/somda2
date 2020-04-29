<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200429122231 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Log route instead of block';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_logging` DROP FOREIGN KEY `FK_8127138D711B2385`');
        $this->addSql('DROP INDEX `IDX_8127138D711B2385` ON `somda_logging`');
        $this->addSql('
            ALTER TABLE `somda_logging`
            DROP `blokid`,
            ADD `route` VARCHAR(255) NOT NULL,
            ADD route_parameters LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'
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
