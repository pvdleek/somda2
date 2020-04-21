<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200421074829 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Increase security level for users';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE `somda_users` SET `password` = \'\'');
        $this->addSql('
            ALTER TABLE `somda_users`
            CHANGE `password` `password` VARCHAR(255) NOT NULL,
            CHANGE `actkey` `actkey` VARCHAR(13) DEFAULT NULL
        ');
        $this->addSql('UPDATE `somda_users` SET `actkey` = NULL WHERE `actkey` = \'0\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
