<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\UpdateStatisticsCommand;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateStatisticsCommandTest extends TestCase
{
    private UpdateStatisticsCommand $command;
    private Connection $connection;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->command = new UpdateStatisticsCommand($this->connection);
    }

    public function testExecuteUpdatesStatistics(): void
    {
        $statement = $this->createMock(Statement::class);
        $statement->expects($this->atLeastOnce())
            ->method('bindValue');
        $statement->expects($this->atLeastOnce())
            ->method('executeStatement');

        $this->connection
            ->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturn($statement);

        $command_tester = new CommandTester($this->command);
        $result = $command_tester->execute([]);

        $this->assertEquals(0, $result);
    }

    public function testCommandName(): void
    {
        $this->assertEquals('app:update-statistics', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals('Update statistics', $this->command->getDescription());
    }
}
