<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200415092301 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Links trains (somda_mat) to naming patterns';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_mat` ADD `pattern_id` BIGINT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE `somda_mat` ADD CONSTRAINT `FK_355CF79F734A20F`
            FOREIGN KEY (`pattern_id`) REFERENCES `somda_mat_patterns` (`id`)
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
