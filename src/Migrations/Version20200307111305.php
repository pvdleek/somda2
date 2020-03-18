<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111305 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Cleanup train-tables';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // Cleanup
        $this->addSql('DELETE FROM somda_tdr WHERE locatieid NOT IN (SELECT afkid FROM somda_verk)');
        $this->addSql(
            'DELETE FROM somda_tdr_route WHERE treinnummerlijst_id NOT IN (SELECT id FROM somda_tdr_treinnummerlijst)'
        );
        $this->addSql('DELETE FROM somda_tdr_route WHERE locatieid NOT IN (SELECT afkid FROM somda_verk)');
        $this->addSql('DELETE FROM somda_tdr_trein_mat WHERE mat_naam_id NOT IN (SELECT id FROM somda_mat_naam)');
        $this->addSql(
            'ALTER TABLE somda_tdr_trein_mat CHANGE COLUMN mat_type_id mat_type_id BIGINT(20) DEFAULT 0 NOT NULL'
        );
        $this->addSql('UPDATE somda_tdr_trein_mat SET mat_type_id = NULL WHERE mat_type_id = 0');
        $this->addSql('UPDATE somda_tdr_s_e SET a_locatieid = 974 WHERE treinid = 8375 AND a_locatieid = 0');

        // Fix primary keys
        $this->addSql('
            ALTER TABLE somda_tdr_trein_treinnummerlijst
            DROP PRIMARY KEY, ADD PRIMARY KEY (treinnummerlijst_id, treinid)
        ');
        $this->addSql('
            ALTER TABLE somda_tdr_route
            DROP PRIMARY KEY, ADD PRIMARY KEY (type, volgorde, tdr_nr, treinnummerlijst_id)
        ');
        $this->addSql(
            'ALTER TABLE somda_tdr_trein_mat DROP PRIMARY KEY, ADD PRIMARY KEY (dag, tdr_nr, treinid, posid)'
        );
        $this->addSql('ALTER TABLE somda_tdr_s_e DROP PRIMARY KEY, ADD PRIMARY KEY (dag, tdr_nr, treinid)');
        $this->addSql('ALTER TABLE somda_tdr_in_s_e DROP PRIMARY KEY, ADD PRIMARY KEY (dag, treinid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
