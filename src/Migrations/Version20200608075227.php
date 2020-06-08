<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200608075227 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Add index on active for users';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE INDEX `idx_49076_active` ON `somda_users` (`active`)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
