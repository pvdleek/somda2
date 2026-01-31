<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\GetRailNewsCommand;
use App\Entity\RailNewsSourceFeed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GetRailNewsCommandTest extends TestCase
{
    private GetRailNewsCommand $command;
    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entity_manager;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->entity_manager = $this->createMock(EntityManagerInterface::class);
        $this->command = new GetRailNewsCommand($this->doctrine);
    }

    public function testExecuteWithNoFeeds(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->with(RailNewsSourceFeed::class)
            ->willReturn($repository);

        $this->doctrine
            ->expects($this->never())
            ->method('getManager');

        $command_tester = new CommandTester($this->command);
        $result = $command_tester->execute([]);

        $this->assertEquals(0, $result);
    }

    public function testCommandName(): void
    {
        $this->assertEquals('app:get-rail-news', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals(
            'Read all the rail-news providers and process the news items',
            $this->command->getDescription()
        );
    }
}
