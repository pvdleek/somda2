<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\FixTrainTableFirstLastCommand;
use App\Entity\TrainTableYear;
use App\Repository\TrainTableYearRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FixTrainTableFirstLastCommandTest extends TestCase
{
    private const NO_DAYS = ['ma' => 0, 'di' => 0, 'wo' => 0, 'do' => 0, 'vr' => 0, 'za' => 0, 'zf' => 0];

    private FixTrainTableFirstLastCommand $command;
    private Connection $connection;
    private TrainTableYearRepository $train_table_year_repository;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->train_table_year_repository = $this->createMock(TrainTableYearRepository::class);
        $this->command = new FixTrainTableFirstLastCommand($this->connection, $this->train_table_year_repository);

        $train_table_year = new TrainTableYear();
        $train_table_year->id = 18;
        $train_table_year->name = 'Test';
        $this->train_table_year_repository->method('findTrainTableYearByDate')->willReturn($train_table_year);
    }

    public function testExecuteInsertsUpdatesAndDeletes(): void
    {
        // Route 1 runs on monday (day 1) from location 10 to 30 and on tuesday (day 2) from 40 to 50
        $train_table_rows = [
            ['treinid' => 1, 'locatieid' => 10, 'actie' => 'v', 'tijd' => 100, 'ma' => 1] + self::NO_DAYS,
            ['treinid' => 1, 'locatieid' => 40, 'actie' => 'v', 'tijd' => 105, 'di' => 1] + self::NO_DAYS,
            ['treinid' => 1, 'locatieid' => 20, 'actie' => '+', 'tijd' => 110, 'ma' => 1] + self::NO_DAYS,
            ['treinid' => 1, 'locatieid' => 50, 'actie' => 'a', 'tijd' => 115, 'di' => 1] + self::NO_DAYS,
            ['treinid' => 1, 'locatieid' => 30, 'actie' => 'a', 'tijd' => 120, 'ma' => 1] + self::NO_DAYS,
        ];
        $result = $this->createMock(Result::class);
        $result->method('fetchAssociative')->willReturnOnConsecutiveCalls(...[...$train_table_rows, false]);
        $this->connection->method('executeQuery')->willReturn($result);

        // Monday is wrong, tuesday is missing and route 2 has no train-tables
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['treinid' => 1, 'dag' => 1, 'v_locatieid' => 99, 'v_actie' => 'v', 'v_tijd' => 100,
                'a_locatieid' => 30, 'a_actie' => 'a', 'a_tijd' => 120],
            ['treinid' => 2, 'dag' => 3, 'v_locatieid' => 10, 'v_actie' => 'v', 'v_tijd' => 100,
                'a_locatieid' => 30, 'a_actie' => 'a', 'a_tijd' => 120],
        ]);

        $this->connection->expects($this->once())->method('update')->with(
            'somda_tdr_s_e',
            ['v_locatieid' => 10, 'v_actie' => 'v', 'v_tijd' => 100, 'a_locatieid' => 30, 'a_actie' => 'a', 'a_tijd' => 120],
            ['tdr_nr' => 18, 'treinid' => 1, 'dag' => 1]
        );
        $this->connection->expects($this->once())->method('insert')->with(
            'somda_tdr_s_e',
            ['tdr_nr' => 18, 'treinid' => 1, 'dag' => 2, 'v_locatieid' => 40, 'v_actie' => 'v', 'v_tijd' => 105,
                'a_locatieid' => 50, 'a_actie' => 'a', 'a_tijd' => 115]
        );
        $this->connection->expects($this->once())->method('delete')->with(
            'somda_tdr_s_e',
            ['tdr_nr' => 18, 'treinid' => 2, 'dag' => 3]
        );
        $this->connection->expects($this->once())->method('commit');

        $command_tester = new CommandTester($this->command);
        $this->assertEquals(0, $command_tester->execute([]));
        $this->assertStringContainsString('Inserted: 1, updated: 1, deleted: 1', $command_tester->getDisplay());
    }

    public function testDryRunDoesNotChangeAnything(): void
    {
        $result = $this->createMock(Result::class);
        $result->method('fetchAssociative')->willReturnOnConsecutiveCalls(
            ['treinid' => 1, 'locatieid' => 10, 'actie' => 'v', 'tijd' => 100, 'ma' => 1] + self::NO_DAYS,
            false
        );
        $this->connection->method('executeQuery')->willReturn($result);
        $this->connection->method('fetchAllAssociative')->willReturn([]);

        $this->connection->expects($this->never())->method('beginTransaction');
        $this->connection->expects($this->never())->method('insert');
        $this->connection->expects($this->never())->method('update');
        $this->connection->expects($this->never())->method('delete');

        $command_tester = new CommandTester($this->command);
        $this->assertEquals(0, $command_tester->execute(['--dry-run' => true]));
        $this->assertStringContainsString('Inserted: 1, updated: 0, deleted: 0', $command_tester->getDisplay());
    }

    public function testUnknownTrainTableYearFails(): void
    {
        $this->train_table_year_repository->method('find')->willReturn(null);

        $command_tester = new CommandTester($this->command);
        $this->assertEquals(1, $command_tester->execute(['--year' => '999']));
    }

    public function testCommandName(): void
    {
        $this->assertEquals('app:fix-train-table-first-last', $this->command->getName());
    }
}
