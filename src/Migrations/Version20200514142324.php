<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200514142324 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Change IP fields of banner-hits to an integer';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_banner_hits` ADD `ip_address` BIGINT NOT NULL');
        $this->addSql('UPDATE `somda_banner_hits` SET `ip_address` = INET_ATON(`ip`)');
        $this->addSql('ALTER TABLE `somda_banner_hits` DROP `ip`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
