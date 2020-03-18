<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200307111304 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Combine all train-tables in 1';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE somda_tdr_1 RENAME TO somda_tdr');
        $this->addSql('ALTER TABLE somda_tdr ADD tdr_nr INTEGER DEFAULT 1 NOT NULL');

        $this->addSql('ALTER TABLE somda_tdr_1_in RENAME TO somda_tdr_in');
        $this->addSql('ALTER TABLE somda_tdr_in ADD tdr_nr INTEGER NOT NULL');

        $this->addSql('ALTER TABLE somda_tdr_1_in_s_e RENAME TO somda_tdr_in_s_e');
        $this->addSql('ALTER TABLE somda_tdr_in_s_e ADD tdr_nr INTEGER NOT NULL');

        $this->addSql('ALTER TABLE somda_tdr_1_route RENAME TO somda_tdr_route');
        $this->addSql('ALTER TABLE somda_tdr_route ADD tdr_nr INTEGER DEFAULT 1 NOT NULL');
        $this->addSql('
            ALTER TABLE somda_tdr_route
            DROP PRIMARY KEY, ADD PRIMARY KEY (tdr_nr, treinnummerlijst_id, type, volgorde)
        ');

        $this->addSql('ALTER TABLE somda_tdr_1_s_e RENAME TO somda_tdr_s_e');
        $this->addSql('ALTER TABLE somda_tdr_s_e ADD tdr_nr INTEGER DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE somda_tdr_s_e DROP PRIMARY KEY, ADD PRIMARY KEY (tdr_nr, treinid, dag)');

        $this->addSql('ALTER TABLE somda_tdr_1_treinnummerlijst RENAME TO somda_tdr_treinnummerlijst');
        $this->addSql('ALTER TABLE somda_tdr_treinnummerlijst ADD tdr_nr INTEGER DEFAULT 1 NOT NULL');

        $this->addSql('ALTER TABLE somda_tdr_1_trein_mat RENAME TO somda_tdr_trein_mat');
        $this->addSql('ALTER TABLE somda_tdr_trein_mat ADD tdr_nr INTEGER DEFAULT 1 NOT NULL');
        $this->addSql(
            'ALTER TABLE somda_tdr_trein_mat DROP PRIMARY KEY, ADD PRIMARY KEY (tdr_nr, treinid, posid, dag)'
        );

        $this->addSql('ALTER TABLE somda_tdr_1_trein_treinnummerlijst RENAME TO somda_tdr_trein_treinnummerlijst');
        $this->addSql('ALTER TABLE somda_tdr_treinnummerlijst ADD COLUMN old_id INTEGER DEFAULT 1');

        for ($tdrNr = 2; $tdrNr <= 12; ++$tdrNr) {
            $this->addSql('
                INSERT INTO somda_tdr (tdr_nr, orderid, treinid, rijdagenid, locatieid, actie, tijd, spoor)
                SELECT ' . $tdrNr . ', orderid, treinid, rijdagenid, locatieid, actie, tijd, spoor
                    FROM somda_tdr_' . $tdrNr . '
            ');
            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr);

            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr . '_in');
            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr . '_in_s_e');

            $this->addSql('
                INSERT INTO somda_tdr_s_e (
                    tdr_nr, treinid, dag, v_locatieid, v_actie, v_tijd, a_locatieid, a_actie, a_tijd
                )
                SELECT ' . $tdrNr . ', treinid, dag, v_locatieid, v_actie, v_tijd, a_locatieid, a_actie, a_tijd
                    FROM somda_tdr_' . $tdrNr . '_s_e
            ');
            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr . '_s_e');

            $this->addSql('
                INSERT INTO somda_tdr_treinnummerlijst (
                    old_id, tdr_nr, nr_start, nr_eind, vervoerder_id, karakteristiek_id, traject
                )
                SELECT id, ' . $tdrNr . ', nr_start, nr_eind, vervoerder_id, karakteristiek_id, traject
                    FROM somda_tdr_' . $tdrNr . '_treinnummerlijst
            ');

            $this->addSql('
                INSERT INTO somda_tdr_route (tdr_nr, treinnummerlijst_id, type, volgorde, locatieid)
                SELECT ' . $tdrNr . ', id, type, volgorde, locatieid
                    FROM somda_tdr_' . $tdrNr . '_route
                    JOIN somda_tdr_treinnummerlijst ON old_id = treinnummerlijst_id
            ');
            $this->addSql('
                INSERT IGNORE INTO somda_tdr_trein_treinnummerlijst (treinnummerlijst_id, treinid)
                SELECT id, treinid
                    FROM somda_tdr_' . $tdrNr . '_trein_treinnummerlijst
                    JOIN somda_tdr_treinnummerlijst ON old_id = treinnummerlijst_id
            ');

            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr . '_route');
            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr . '_treinnummerlijst');

            $this->addSql('
                INSERT INTO somda_tdr_trein_mat (tdr_nr, treinid, posid, dag, mat_naam_id, mat_type_id, spots)
                SELECT ' . $tdrNr . ', treinid, posid, dag, mat_naam_id, mat_type_id, spots
                    FROM somda_tdr_' . $tdrNr . '_trein_mat
            ');
            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr . '_trein_mat');

            $this->addSql('DROP TABLE somda_tdr_' . $tdrNr . '_trein_treinnummerlijst');
        }

        $this->addSql('ALTER TABLE somda_tdr_treinnummerlijst DROP old_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // Not applicable
    }
}
