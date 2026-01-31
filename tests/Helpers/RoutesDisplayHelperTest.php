<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\RouteList;
use App\Entity\TrainTableYear;
use App\Helpers\RoutesDisplayHelper;
use App\Repository\RouteListRepository;
use App\Repository\TrainTableYearRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RoutesDisplayHelperTest extends TestCase
{
    private RoutesDisplayHelper $routes_display_helper;
    private ManagerRegistry $doctrine;
    private RouteListRepository $route_list_repository;
    private TrainTableYearRepository $train_table_year_repository;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->route_list_repository = $this->createMock(RouteListRepository::class);
        $this->train_table_year_repository = $this->createMock(TrainTableYearRepository::class);

        $this->routes_display_helper = new RoutesDisplayHelper(
            $this->doctrine,
            $this->route_list_repository,
            $this->train_table_year_repository
        );
    }

    public function testGetRoutesDisplayWithNullTrainTableYear(): void
    {
        $train_table_year = $this->createMock(TrainTableYear::class);

        $this->train_table_year_repository
            ->expects($this->once())
            ->method('findTrainTableYearByDate')
            ->with($this->isInstanceOf(\DateTime::class))
            ->willReturn($train_table_year);

        $result = $this->routes_display_helper->getRoutesDisplay();

        $this->assertSame($train_table_year, $result->train_table_year);
    }

    public function testGetRoutesDisplayWithZeroTrainTableYear(): void
    {
        $train_table_year = $this->createMock(TrainTableYear::class);

        $this->train_table_year_repository
            ->expects($this->once())
            ->method('findTrainTableYearByDate')
            ->willReturn($train_table_year);

        $result = $this->routes_display_helper->getRoutesDisplay(0);

        $this->assertSame($train_table_year, $result->train_table_year);
    }

    public function testGetRoutesDisplayWithValidTrainTableYear(): void
    {
        $train_table_year = $this->createMock(TrainTableYear::class);

        $this->train_table_year_repository
            ->expects($this->once())
            ->method('find')
            ->with(123)
            ->willReturn($train_table_year);

        $this->route_list_repository
            ->expects($this->once())
            ->method('findForOverview')
            ->with($train_table_year)
            ->willReturn([]);

        $result = $this->routes_display_helper->getRoutesDisplay(123);

        $this->assertSame($train_table_year, $result->train_table_year);
        $this->assertIsArray($result->route_lists);
    }

    public function testGetRoutesDisplayWithInvalidTrainTableYearThrowsException(): void
    {
        $this->train_table_year_repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('This train_table_year does not exist');

        $this->routes_display_helper->getRoutesDisplay(999);
    }

    public function testGetRoutesDisplayWithValidRouteList(): void
    {
        $train_table_year = $this->createMock(TrainTableYear::class);
        $route_list = $this->createMock(RouteList::class);
        $route_list->method('getRoutes')->willReturn([]);

        $this->train_table_year_repository
            ->expects($this->once())
            ->method('find')
            ->with(123)
            ->willReturn($train_table_year);

        $this->route_list_repository
            ->expects($this->once())
            ->method('findForOverview')
            ->with($train_table_year)
            ->willReturn([]);

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(456)
            ->willReturn($route_list);

        $this->doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->with(RouteList::class)
            ->willReturn($repository);

        $result = $this->routes_display_helper->getRoutesDisplay(123, 456);

        $this->assertSame($train_table_year, $result->train_table_year);
        $this->assertSame($route_list, $result->selected_route_list);
        $this->assertIsArray($result->routes);
    }

    public function testGetRoutesDisplayWithInvalidRouteListThrowsException(): void
    {
        $train_table_year = $this->createMock(TrainTableYear::class);

        $this->train_table_year_repository
            ->expects($this->once())
            ->method('find')
            ->with(123)
            ->willReturn($train_table_year);

        $this->route_list_repository
            ->expects($this->once())
            ->method('findForOverview')
            ->with($train_table_year)
            ->willReturn([]);

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->doctrine
            ->expects($this->once())
            ->method('getRepository')
            ->with(RouteList::class)
            ->willReturn($repository);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('This route_list does not exist');

        $this->routes_display_helper->getRoutesDisplay(123, 999);
    }
}
