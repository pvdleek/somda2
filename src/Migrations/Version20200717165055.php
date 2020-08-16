<?php
declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200717165055 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Add API-token to the user table';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE `somda_users`
            ADD `api_token` CHAR(23) DEFAULT NULL,
            ADD `api_token_expiry_timestamp` DATETIME DEFAULT NULL
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `somda_users` DROP COLUMN `api_token`, DROP COLUMN `api_token_expiry_timestamp`');
    }
}
