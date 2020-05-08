<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200430131854 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Change relations from routeTrains';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `somda_tdr_trein_mat` DROP FOREIGN KEY `FK_C2BF79AA394F068`');
        $this->addSql('ALTER TABLE `somda_tdr_trein_mat` DROP FOREIGN KEY `FK_C2BF79AAB78B25C3`');
        $this->addSql('DROP INDEX `IDX_C2BF79AAB78B25C3` ON `somda_tdr_trein_mat`');
        $this->addSql('DROP INDEX `IDX_C2BF79AA394F068` ON `somda_tdr_trein_mat`');
        $this->addSql('
            ALTER TABLE `somda_tdr_trein_mat`
            ADD `mat_pattern_id` BIGINT DEFAULT NULL,
            DROP `mat_naam_id`,
            DROP `mat_type_id`
        ');
        $this->addSql('
            ALTER TABLE `somda_tdr_trein_mat` ADD CONSTRAINT `FK_C2BF79AAE14DDC7E`
            FOREIGN KEY (`mat_pattern_id`) REFERENCES `somda_mat_patterns` (`id`)
        ');
        $this->addSql('CREATE INDEX `IDX_C2BF79AAE14DDC7E` ON `somda_tdr_trein_mat` (`mat_pattern_id`)');

        $this->addSql('DROP TABLE `somda_mat_naam`');
        $this->addSql('DROP TABLE `somda_mat_type_patterns`');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
