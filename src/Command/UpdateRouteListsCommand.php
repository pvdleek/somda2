<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-route-lists',
    description: 'Update route-lists',
    hidden: false,
)]

class UpdateRouteListsCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $query = 'DELETE FROM `somda_tdr_trein_treinnummerlijst`';
        $statement = $this->connection->prepare($query);
        $statement->executeStatement();

        $query = 'INSERT INTO `somda_tdr_trein_treinnummerlijst` (`treinnummerlijst_id`, `treinid`)
            SELECT `l`.`id`, `tr`.`treinid`
                FROM `somda_tdr_treinnummerlijst` `l`
                JOIN `somda_trein` `tr` ON IFNULL(CAST(REGEXP_REPLACE(`tr`.`treinnr`, \'[^0-9]+\', \'\') AS DOUBLE), NULL) BETWEEN `l`.`nr_start` AND `l`.`nr_eind`
                JOIN `somda_tdr_s_e` `t` ON `t`.`treinid` = `tr`.`treinid` AND `t`.`tdr_nr` = `l`.`tdr_nr`
                GROUP BY `tr`.`treinid`, `l`.`id`';
        $statement = $this->connection->prepare($query);
        $statement->executeStatement();

        // Remove all routes that no longer exist
        $query = 'DELETE FROM `somda_tdr_trein_treinnummerlijst`
            WHERE `treinnummerlijst_id` NOT IN (SELECT `id` FROM `somda_tdr_treinnummerlijst`)';
        $statement = $this->connection->prepare($query);
        $statement->executeStatement();

        return 0;
    }
}
