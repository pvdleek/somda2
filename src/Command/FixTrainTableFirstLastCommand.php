<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\TrainTableYear;
use App\Repository\TrainTableYearRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fix-train-table-first-last',
    description: 'Correct the first/last train-table records based on the train-tables',
    hidden: false,
)]

class FixTrainTableFirstLastCommand extends Command
{
    private const OPTION_YEAR = 'year';
    private const OPTION_DRY_RUN = 'dry-run';

    /**
     * Day-number (1 = monday ... 7 = sunday) to the column in somda_rijdagen
     */
    private const DAY_COLUMNS = [1 => 'ma', 2 => 'di', 3 => 'wo', 4 => 'do', 5 => 'vr', 6 => 'za', 7 => 'zf'];

    private const FIELDS = ['v_locatieid', 'v_actie', 'v_tijd', 'a_locatieid', 'a_actie', 'a_tijd'];

    public function __construct(
        private readonly Connection $connection,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                self::OPTION_YEAR,
                'y',
                InputOption::VALUE_REQUIRED,
                'The id of the train-table-year to process (default: the current train-table-year)'
            )
            ->addOption(self::OPTION_DRY_RUN, 'd', InputOption::VALUE_NONE, 'Only report, do not change anything');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $train_table_year = $this->getTrainTableYear($input->getOption(self::OPTION_YEAR));
        if (null === $train_table_year) {
            $output->writeln('<error>Train-table-year not found</error>');
            return Command::FAILURE;
        }
        $dry_run = $input->getOption(self::OPTION_DRY_RUN) === true;

        $output->writeln(
            'Processing train-table-year '.$train_table_year->name.' ('.$train_table_year->id.')'
            .($dry_run ? ' [dry-run]' : '')
        );

        $expected = $this->getExpectedFirstLasts($train_table_year->id);
        $existing = $this->getExistingFirstLasts($train_table_year->id);

        $inserted = $updated = $deleted = 0;

        if (!$dry_run) {
            $this->connection->beginTransaction();
        }
        try {
            foreach ($expected as $key => $values) {
                [$route_id, $day_number] = \array_map('intval', \explode('-', $key));
                $identifier = ['tdr_nr' => $train_table_year->id, 'treinid' => $route_id, 'dag' => $day_number];

                if (!isset($existing[$key])) {
                    ++$inserted;
                    if (!$dry_run) {
                        $this->connection->insert('somda_tdr_s_e', $identifier + $values);
                    }
                } elseif ($existing[$key] !== $values) {
                    ++$updated;
                    if ($output->isVerbose()) {
                        $output->writeln(
                            'Update route '.$route_id.' day '.$day_number.': '
                            .\json_encode($existing[$key]).' => '.\json_encode($values)
                        );
                    }
                    if (!$dry_run) {
                        $this->connection->update('somda_tdr_s_e', $values, $identifier);
                    }
                }
                unset($existing[$key]);
            }

            // Whatever is left has no train-table records (anymore) for that day
            foreach (\array_keys($existing) as $key) {
                [$route_id, $day_number] = \array_map('intval', \explode('-', (string) $key));
                ++$deleted;
                if (!$dry_run) {
                    $this->connection->delete(
                        'somda_tdr_s_e',
                        ['tdr_nr' => $train_table_year->id, 'treinid' => $route_id, 'dag' => $day_number]
                    );
                }
            }

            if (!$dry_run) {
                $this->connection->commit();
            }
        } catch (\Throwable $exception) {
            if (!$dry_run) {
                $this->connection->rollBack();
            }
            throw $exception;
        }

        $output->writeln('Inserted: '.$inserted.', updated: '.$updated.', deleted: '.$deleted);

        return Command::SUCCESS;
    }

    private function getTrainTableYear(mixed $year_id): ?TrainTableYear
    {
        if (null !== $year_id) {
            return $this->train_table_year_repository->find((int) $year_id);
        }
        return $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime());
    }

    /**
     * Determine the first and last train-table record per route per day, the same way as the RouteManagementHelper
     * @return array<string, array<string, int|string|null>>
     * @throws \Doctrine\DBAL\Exception
     */
    private function getExpectedFirstLasts(int $train_table_year_id): array
    {
        $query = 'SELECT `t`.`treinid`, `t`.`locatieid`, `t`.`actie`, `t`.`tijd`, '
            .\implode(', ', \array_map(fn (string $column) => '`r`.`'.$column.'`', self::DAY_COLUMNS))
            .' FROM `somda_tdr` `t`
            JOIN `somda_rijdagen` `r` ON `r`.`rijdagenid` = `t`.`rijdagenid`
            WHERE `t`.`tdr_nr` = :train_table_year_id
            ORDER BY `t`.`treinid`, `t`.`orderid`, `t`.`tdrid`';
        $result = $this->connection->executeQuery($query, ['train_table_year_id' => $train_table_year_id]);

        $expected = [];
        while (false !== ($row = $result->fetchAssociative())) {
            foreach (self::DAY_COLUMNS as $day_number => $column) {
                if (!(bool) $row[$column]) {
                    continue;
                }
                $key = $row['treinid'].'-'.$day_number;
                if (!isset($expected[$key])) {
                    $expected[$key] = [
                        'v_locatieid' => self::toNullableInt($row['locatieid']),
                        'v_actie' => (string) $row['actie'],
                        'v_tijd' => (int) $row['tijd'],
                    ];
                }
                $expected[$key]['a_locatieid'] = self::toNullableInt($row['locatieid']);
                $expected[$key]['a_actie'] = (string) $row['actie'];
                $expected[$key]['a_tijd'] = (int) $row['tijd'];
            }
        }

        return $expected;
    }

    /**
     * @return array<string, array<string, int|string|null>>
     * @throws \Doctrine\DBAL\Exception
     */
    private function getExistingFirstLasts(int $train_table_year_id): array
    {
        $query = 'SELECT `treinid`, `dag`, `'.\implode('`, `', self::FIELDS).'`
            FROM `somda_tdr_s_e` WHERE `tdr_nr` = :train_table_year_id';
        $rows = $this->connection->fetchAllAssociative($query, ['train_table_year_id' => $train_table_year_id]);

        $existing = [];
        foreach ($rows as $row) {
            $existing[$row['treinid'].'-'.$row['dag']] = [
                'v_locatieid' => self::toNullableInt($row['v_locatieid']),
                'v_actie' => (string) $row['v_actie'],
                'v_tijd' => (int) $row['v_tijd'],
                'a_locatieid' => self::toNullableInt($row['a_locatieid']),
                'a_actie' => (string) $row['a_actie'],
                'a_tijd' => (int) $row['a_tijd'],
            ];
        }

        return $existing;
    }

    private static function toNullableInt(mixed $value): ?int
    {
        return null === $value ? null : (int) $value;
    }
}
