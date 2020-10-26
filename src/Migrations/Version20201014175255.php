<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201014175255 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Migrate table-names to correct structure - forum-read';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        for ($table = 0; $table <= 9; ++$table) {
            $this->addSql('
                ALTER TABLE fr' . $table . '_forum_read_' . $table . '
                    DROP FOREIGN KEY FK_forum_read_post_' . $table . ';
                ALTER TABLE fr' . $table . '_forum_read_' . $table . '
                    DROP FOREIGN KEY FK_forum_read_user_' . $table . ';
                DROP INDEX somda_forum_read_' . $table . '_idx_uid ON fr' . $table . '_forum_read_' . $table . ';
                DROP INDEX IDX_forum_read_' . $table . ' ON fr' . $table . '_forum_read_' . $table . ';
                ALTER TABLE fr' . $table . '_forum_read_' . $table . ' DROP PRIMARY KEY;
                ALTER TABLE fr' . $table . '_forum_read_' . $table . '
                    CHANGE postid fr' . $table . '_fop_id BIGINT NOT NULL,
                    CHANGE uid fr' . $table . '_use_id BIGINT NOT NULL;
                ALTER TABLE fr' . $table . '_forum_read_' . $table . ' ADD CONSTRAINT FRK_fr' . $table . '_fop_id
                    FOREIGN KEY (fr' . $table . '_fop_id) REFERENCES fop_forum_post (fop_id);
                ALTER TABLE fr' . $table . '_forum_read_' . $table . ' ADD CONSTRAINT FRK_fr' . $table . '_use_id
                    FOREIGN KEY (fr' . $table . '_use_id) REFERENCES use_user (use_id);
                CREATE INDEX IDX_fr' . $table . '_fop_id
                    ON fr' . $table . '_forum_read_' . $table . ' (fr' . $table . '_fop_id);
                CREATE INDEX IDX_fr' . $table . '_use_id
                    ON fr' . $table . '_forum_read_' . $table . ' (fr' . $table . '_use_id);
                ALTER TABLE fr' . $table . '_forum_read_' . $table . '
                    ADD PRIMARY KEY (fr' . $table . '_fop_id, fr' . $table . '_use_id);
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
