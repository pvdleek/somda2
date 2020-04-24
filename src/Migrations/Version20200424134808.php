<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200424134808 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Remove use of BB-codes and percent-codes in forum-posts';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('
            UPDATE `somda_forum_posts_text` SET `text` =
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(`text`, \'[b]\', \'</strong>\'),
                                            \'[/b]\', \'</strong>\'
                                        ), \'[i]\', \'<em>\'
                                    ), \'[/i]\', \'</em>\'
                                ), \'[u]\', \'<u>\'
                            ), \'[/u]\', \'</u>\'
                        ), \'[s]\', \'<s>\'
                    ), \'[/s]\', \'</s>\'
                )
        ');
        $this->addSql('
            UPDATE `somda_forum_posts_text` SET `text` =
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(`text`, \'[B]\', \'</strong>\'),
                                            \'[/B]\', \'</strong>\'
                                        ), \'[I]\', \'<em>\'
                                    ), \'[/I]\', \'</em>\'
                                ), \'[U]\', \'<u>\'
                            ), \'[/U]\', \'</u>\'
                        ), \'[S]\', \'<s>\'
                    ), \'[/S]\', \'</s>\'
                )
        ');

        $this->addSql('
            UPDATE `somda_forum_posts_text` SET `text` =
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(`text`, \'%b%\', \'</strong>\'),
                                \'%/b%\', \'</strong>\'
                            ), \'%i%\', \'<em>\'
                        ), \'%/i%\', \'</em>\'
                    )
        ');
        $this->addSql('
            UPDATE `somda_forum_posts_text` SET `text` =
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(`text`, \'%B%\', \'</strong>\'),
                                \'%/B%\', \'</strong>\'
                            ), \'%I%\', \'<em>\'
                        ), \'%/I%\', \'</em>\'
                    )
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
