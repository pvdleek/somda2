<?php
declare(strict_types=1);

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateRouteListsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:update-route-lists';

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
        $this->setDescription('Update route-lists');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        $connection = $this->doctrine->getManager()->getConnection();

        $query = 'DELETE FROM `somda_tdr_trein_treinnummerlijst`';
        $statement = $connection->prepare($query);
        $statement->executeStatement();

        $query = 'INSERT INTO `somda_tdr_trein_treinnummerlijst` (`treinnummerlijst_id`, `treinid`)
        SELECT `l`.`id`, `tr`.`treinid`
            FROM `somda_tdr_treinnummerlijst` `l`
            JOIN `somda_trein` `tr` ON `tr`.`treinnr` BETWEEN `l`.`nr_start` AND `l`.`nr_eind`
            JOIN `somda_tdr_s_e` `t` ON `t`.`treinid` = `tr`.`treinid` AND `t`.`tdr_nr` = `l`.`tdr_nr`
            GROUP BY `tr`.`treinid`, `l`.`id`';
        $statement = $connection->prepare($query);
        $statement->executeStatement();

        // Remove all routes that no longer exist
        $query = 'DELETE FROM `somda_tdr_trein_treinnummerlijst`
			    WHERE `treinnummerlijst_id` NOT IN (SELECT `id` FROM `somda_tdr_treinnummerlijst`)';
        $statement = $connection->prepare($query);
        $statement->executeStatement();

        return 0;
    }
}
