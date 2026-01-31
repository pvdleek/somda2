<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\UpdateLocationsCommand;
use App\Entity\Location;
use App\Entity\LocationCategory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateLocationsCommandTest extends TestCase
{
    private UpdateLocationsCommand $command;
    private ManagerRegistry $doctrine;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->command = new UpdateLocationsCommand($this->doctrine);
    }

    public function testCommandName(): void
    {
        $this->assertEquals('app:update-locations', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals(
            'Update the locations from the official list at NS',
            $this->command->getDescription()
        );
    }
}
