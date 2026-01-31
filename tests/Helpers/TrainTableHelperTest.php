<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\TrainTableYear;
use App\Helpers\TrainTableHelper;
use App\Repository\LocationRepository;
use App\Repository\TrainTableRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrainTableHelperTest extends TestCase
{
    private TrainTableHelper $train_table_helper;
    private ManagerRegistry $doctrine;
    private TranslatorInterface $translator;
    private LocationRepository $location_repository;
    private TrainTableRepository $train_table_repository;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->location_repository = $this->createMock(LocationRepository::class);
        $this->train_table_repository = $this->createMock(TrainTableRepository::class);

        $this->train_table_helper = new TrainTableHelper(
            $this->doctrine,
            $this->translator,
            $this->location_repository,
            $this->train_table_repository
        );
    }

    public function testSetAndGetTrainTableYear(): void
    {
        $train_table_year = $this->createMock(TrainTableYear::class);
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(123)
            ->willReturn($train_table_year);

        $this->doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->with(TrainTableYear::class)
            ->willReturn($repository);

        $this->train_table_helper->setTrainTableYear(123);

        $result = $this->train_table_helper->getTrainTableYear();

        $this->assertSame($train_table_year, $result);
    }

    public function testGetTrainTableYearWhenNotSet(): void
    {
        $result = $this->train_table_helper->getTrainTableYear();

        $this->assertNull($result);
    }

    public function testSetAndGetRoute(): void
    {
        $route = $this->createMock(Route::class);
        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => '1234'])
            ->willReturn($route);

        $this->doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->with(Route::class)
            ->willReturn($repository);

        $this->train_table_helper->setRoute('1234');

        $result = $this->train_table_helper->getRoute();

        $this->assertSame($route, $result);
    }

    public function testGetRouteWhenNotSet(): void
    {
        $result = $this->train_table_helper->getRoute();

        $this->assertNull($result);
    }

    public function testSetAndGetLocation(): void
    {
        $location = $this->createMock(Location::class);
        
        $this->location_repository
            ->expects($this->once())
            ->method('findOneByName')
            ->with('Amsterdam')
            ->willReturn($location);

        $this->train_table_helper->setLocation('Amsterdam');

        $result = $this->train_table_helper->getLocation();

        $this->assertSame($location, $result);
    }

    public function testGetLocationWhenNotSet(): void
    {
        $result = $this->train_table_helper->getLocation();

        $this->assertNull($result);
    }

    public function testGetErrorMessagesEmpty(): void
    {
        $result = $this->train_table_helper->getErrorMessages();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testClearErrorMessages(): void
    {
        // Cannot test addErrorMessage as it's private, but we can test clear
        $this->train_table_helper->clearErrorMessages();
        
        $result = $this->train_table_helper->getErrorMessages();

        $this->assertEmpty($result);
    }
}
