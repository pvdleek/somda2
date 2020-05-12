<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200512184812 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription() : string
    {
        return 'Fix some integers, change wikiCheck in forumPosts to an int (from a boolean)';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `somda_verk` CHANGE `route_overstaptijd` `route_overstaptijd` INT DEFAULT NULL');
        $this->addSql('
            ALTER TABLE `somda_banner_hits` ADD CONSTRAINT `FK_8610F3216BBC5658`
            FOREIGN KEY (`bannerid`) REFERENCES `somda_banner` (`bannerid`)
        ');
        $this->addSql('
            ALTER TABLE `somda_api_logging`
            CHANGE `dagnr` `dagnr` INT DEFAULT NULL,
            CHANGE `resultaat_id` `resultaat_id` INT DEFAULT NULL
        ');
        $this->addSql('ALTER TABLE `somda_ddar` CHANGE `stam` `stam` INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `somda_forum_posts` CHANGE `wiki_check` `wiki_check` INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // Not applicable
    }
}
