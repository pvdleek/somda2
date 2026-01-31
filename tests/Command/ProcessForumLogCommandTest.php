<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ProcessForumLogCommand;
use App\Entity\ForumPostLog;
use App\Repository\ForumDiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessForumLogCommandTest extends TestCase
{
    private ProcessForumLogCommand $command;
    private ManagerRegistry $doctrine;
    private ForumDiscussionRepository $forum_discussion_repository;
    private EntityManagerInterface $entity_manager;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->forum_discussion_repository = $this->createMock(ForumDiscussionRepository::class);
        $this->entity_manager = $this->createMock(EntityManagerInterface::class);
        
        $this->command = new ProcessForumLogCommand(
            $this->doctrine,
            $this->forum_discussion_repository
        );
    }

    public function testExecuteWithNoLogs(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with([], ['id' => 'DESC'])
            ->willReturn([]);

        $this->doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->with(ForumPostLog::class)
            ->willReturn($repository);

        $command_tester = new CommandTester($this->command);
        $result = $command_tester->execute([]);

        $this->assertEquals(0, $result);
    }

    public function testCommandName(): void
    {
        $this->assertEquals('app:process-forum-log', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals('Process the forum-log', $this->command->getDescription());
    }
}
