<?php
declare(strict_types=1);

namespace App\Command;

use App\Generics\DateGenerics;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-statistics',
    description: 'Update statistics',
    hidden: false,
)]

class UpdateStatisticsCommand extends Command
{
    private const DATE_PERIOD_WEEK_AGO = 'weekAgo';
    private const DATE_PERIOD_YESTERDAY = 'yesterday';
    private const DATE_PERIOD_TODAY = 'today';

    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        $today = new \DateTime();
        $yesterday = new \DateTime('-1 day');
        $weekAgo = new \DateTime('-7 days');

        $connection = $this->doctrine->getManager()->getConnection();

        // Re-create the statistics for the last 24 hours
        $query = 'REPLACE INTO `somda_stats` (`datum`, `pageviews`, `pageviews_home`, `pageviews_func`)
		    SELECT DATE(`datumtijd`), COUNT(*), COUNT(IF(`route` = \'home\', 1, NULL)),
		        COUNT(IF(`route` <> \'home\', 1, NULL))
			FROM `somda_logging`
			WHERE DATE(`datumtijd`) >= :'.self::DATE_PERIOD_YESTERDAY.'
			GROUP BY DATE(`datumtijd`)';
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_YESTERDAY, $yesterday->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();

        // Update for the unique visitors
        $query = 'UPDATE `somda_stats` `s` SET `s`.`uniek` =
		    (SELECT COUNT(DISTINCT(`l`.`ip`)) FROM `somda_logging` `l` WHERE DATE(`l`.`datumtijd`) = :'.self::DATE_PERIOD_TODAY.')
			WHERE `s`.`datum` = :'.self::DATE_PERIOD_TODAY;
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_TODAY, $today->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();
        $query = 'UPDATE `somda_stats` `s` SET `s`.`uniek` =
		    (SELECT COUNT(DISTINCT(`l`.`ip`)) FROM `somda_logging` `l` WHERE DATE(`l`.`datumtijd`) = :'.self::DATE_PERIOD_YESTERDAY.')
			WHERE `s`.`datum` = :'.self::DATE_PERIOD_YESTERDAY;
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_YESTERDAY, $yesterday->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();

        // Update for the spots and forum-posts
        $query = 'UPDATE `somda_stats` `s` SET spots =
            (SELECT COUNT(*) FROM `somda_spots` `sp` WHERE `sp`.`datum` = `s`.`datum` AND `sp`.`datum` > :'.self::DATE_PERIOD_WEEK_AGO.')';
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_WEEK_AGO, $weekAgo->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();
        $query = 'UPDATE `somda_stats` `s` SET posts =
            (SELECT COUNT(*) FROM `somda_forum_posts` `f`
            WHERE DATE(`f`.`timestamp`) = `s`.`datum` AND `f`.`timestamp` > :'.self::DATE_PERIOD_WEEK_AGO.')';
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_WEEK_AGO, $weekAgo->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();

        // Update for the block-visits
        $query = 'REPLACE INTO `somda_stats_blokken` (`blokid`, `date`, `pageviews`)
		    SELECT `b`.`blokid`, :'.self::DATE_PERIOD_TODAY.', COUNT(*) FROM `somda_logging` `l`
		    JOIN `somda_blokken` `b` ON `b`.`route` = `l`.`route` 
		    WHERE DATE(`l`.`datumtijd`) = :'.self::DATE_PERIOD_TODAY.' GROUP BY `b`.`blokid`';
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_TODAY, $today->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();
        $query = 'REPLACE INTO `somda_stats_blokken` (`blokid`, `date`, `pageviews`)
		    SELECT `b`.`blokid`, :'.self::DATE_PERIOD_YESTERDAY.', COUNT(*) FROM `somda_logging` `l`
		    JOIN `somda_blokken` `b` ON `b`.`route` = `l`.`route` 
		    WHERE DATE(`l`.`datumtijd`) = :'.self::DATE_PERIOD_YESTERDAY.' GROUP BY `b`.`blokid`';
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_YESTERDAY, $yesterday->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();

        $query = 'DELETE FROM `somda_logging` WHERE `datumtijd` <= :'.self::DATE_PERIOD_WEEK_AGO.'';
        $statement = $connection->prepare($query);
        $statement->bindValue(self::DATE_PERIOD_WEEK_AGO, $weekAgo->format(DateGenerics::DATE_FORMAT_DATABASE));
        $statement->executeStatement();

        return 0;
    }
}
