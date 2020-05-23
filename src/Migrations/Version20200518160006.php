<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200518160006 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Update for train-compositions';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DELETE FROM `somda_mat_types` WHERE `typeid` = 1');
        $this->addSql('ALTER TABLE `somda_mat_sms` CHANGE `extra` `extra` VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE `somda_mat_sms` SET `extra` = NULL WHERE LENGTH(`extra`) < 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
