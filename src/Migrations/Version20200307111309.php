<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111309 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Change fields to reflect ORM and create all necessary foreign keys, part 2';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE somda_verk
            CHANGE hafas_desc hafas_desc VARCHAR(50) DEFAULT NULL,
            CHANGE spot_allowed spot_allowed TINYINT(1) NOT NULL
        ');

        $this->addSql('
            ALTER TABLE somda_users
            CHANGE spots_ok spots_ok INT NOT NULL,
            CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
            CHANGE active active TINYINT(1) NOT NULL
        ');

        $this->addSql('DROP INDEX idx_49122_prorail_desc ON somda_vervoerder');
        $this->addSql('
            ALTER TABLE somda_vervoerder
            CHANGE omschrijving omschrijving VARCHAR(35) NOT NULL,
            CHANGE prorail_desc prorail_desc VARCHAR(35) DEFAULT NULL
        ');

        $this->addSql('ALTER TABLE somda_stats CHANGE datum datum DATE NOT NULL');

        $this->addSql('ALTER TABLE somda_groups CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
