<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200612102626 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Increase length of forum search-words and add a user preference';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_forum_zoeken_woorden` CHANGE `woord` `woord` VARCHAR(50) NOT NULL');

        $this->addSql('
            INSERT INTO `somda_prefs` (`sleutel`, `type`, `description`, `default_value`, `volgorde`)
            VALUES (\'force_desktop\', \'boolean\', \'Ga altijd naar de desktop versie, nooit de mobiele versie\',
             \'0\', 80)
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
