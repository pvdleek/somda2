<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200608163917 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Increase size of several user fields';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            ALTER TABLE `somda_users`
            CHANGE `username` `username` VARCHAR(20) NOT NULL,
            CHANGE `name` `name` VARCHAR(100) DEFAULT NULL,
            CHANGE `email` `email` VARCHAR(100) NOT NULL
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
