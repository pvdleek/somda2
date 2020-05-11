<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200511122555 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add timestamp and feedback-flag to spots';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE `somda_spots` ADD `timestamp` DATETIME NOT NULL, ADD `input_feedback_flag` INT NOT NULL'
        );
        $this->addSql('UPDATE `somda_spots` SET `timestamp` = `datum`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
