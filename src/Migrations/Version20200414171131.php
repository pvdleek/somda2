<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200414171131 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Change the dateTime field (Unix timestamp) in railNews to a timestamp with PHP DateTime format';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `somda_sns_spoor_nieuws` ADD `sns_timestamp` DATETIME NOT NULL');
        $this->addSql('UPDATE `somda_sns_spoor_nieuws` SET `sns_timestamp` = FROM_UNIXTIME(`sns_datumtijd`)');
        $this->addSql('ALTER TABLE `somda_sns_spoor_nieuws` DROP `sns_datumtijd`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
