<?php

namespace App\Command;

use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStatisticsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:update-statistics';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct(self::$defaultName);

        $this->doctrine = $doctrine;
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Update statistics');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $today = new DateTime();
        $yesterday = new DateTime('-1 day');
        $weekAgo = new DateTime('-7 days');
        $yearAgo = new DateTime('-1 year');

        $connection = $this->doctrine->getManager()->getConnection();

        // Re-create the statistics for the last 24 hours
        $query = 'REPLACE INTO `somda_stats` (`datum`, `pageviews`, `pageviews_home`, `pageviews_func`)
		    SELECT DATE(`datumtijd`), COUNT(*), COUNT(IF(`route` = \'home\', 1, NULL)),
		        COUNT(IF(`route` <> \'home\', 1, NULL))
			FROM `somda_logging`
			WHERE DATE(`datumtijd`) >= :yesterday
			GROUP BY DATE(`datumtijd`)';
        $statement = $connection->prepare($query);
        $statement->bindValue('yesterday', $yesterday->format('Y-m-d'));
        $statement->execute();

        // Update for the unique visitors
        $query = 'UPDATE `somda_stats` `s` SET `s`.`uniek` =
		    (SELECT COUNT(DISTINCT(`l`.`ip`)) FROM `somda_logging` `l` WHERE DATE(`l`.`datumtijd`) = :today)
			WHERE `s`.`datum` = :today';
        $statement = $connection->prepare($query);
        $statement->bindValue('today', $today->format('Y-m-d'));
        $statement->execute();
        $query = 'UPDATE `somda_stats` `s` SET `s`.`uniek` =
		    (SELECT COUNT(DISTINCT(`l`.`ip`)) FROM `somda_logging` `l` WHERE DATE(`l`.`datumtijd`) = :yesterday)
			WHERE `s`.`datum` = :yesterday';
        $statement = $connection->prepare($query);
        $statement->bindValue('yesterday', $yesterday->format('Y-m-d'));
        $statement->execute();

        // Update for the spots and forum-posts
        $query = 'UPDATE `somda_stats` `s` SET spots =
            (SELECT COUNT(*) FROM `somda_spots` `sp` WHERE `sp`.`datum` = `s`.`datum` AND `sp`.`datum` > :yearAgo)';
        $statement = $connection->prepare($query);
        $statement->bindValue('yearAgo', $yearAgo->format('Y-m-d'));
        $statement->execute();
        $query = 'UPDATE `somda_stats` `s` SET spots =
            (SELECT COUNT(*) FROM `somda_forum_posts` `f`
            WHERE DATE(`f`.`timestamp`) = `s`.`datum` AND `f`.`timestamp` > :yearAgo)';
        $statement = $connection->prepare($query);
        $statement->bindValue('yearAgo', $yearAgo->format('Y-m-d'));
        $statement->execute();

        // Update for the block-visits
        $query = 'REPLACE INTO `somda_stats_blokken` (`blokid`, `date`, `pageviews`)
		    SELECT `b`.`blokid`, :today, COUNT(*) FROM `somda_logging` `l`
		    JOIN `somda_blokken` `b` ON `b`.`route` = `l`.`route` 
		    WHERE DATE(`l`.`datumtijd`) = :today GROUP BY `b`.`blokid`';
        $statement = $connection->prepare($query);
        $statement->bindValue('today', $today->format('Y-m-d'));
        $statement->execute();
        $query = 'REPLACE INTO `somda_stats_blokken` (`blokid`, `date`, `pageviews`)
		    SELECT `b`.`blokid`, :yesterday, COUNT(*) FROM `somda_logging` `l`
		    JOIN `somda_blokken` `b` ON `b`.`route` = `l`.`route` 
		    WHERE DATE(`l`.`datumtijd`) = :yesterday GROUP BY `b`.`blokid`';
        $statement = $connection->prepare($query);
        $statement->bindValue('yesterday', $yesterday->format('Y-m-d'));
        $statement->execute();

        $query = 'DELETE FROM `somda_logging` WHERE `datumtijd` <= :weekAgo';
        $statement = $connection->prepare($query);
        $statement->bindValue('weekAgo', $weekAgo->format('Y-m-d'));
        $statement->execute();

        return 0;
    }
}
