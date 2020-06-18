<?php

namespace App\Helpers;

use App\Entity\RouteList;
use App\Entity\TrainTableYear;
use App\Model\RoutesDisplay;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RoutesDisplayHelper
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param SortHelper $sortHelper
     */
    public function __construct(ManagerRegistry $doctrine, SortHelper $sortHelper)
    {
        $this->doctrine = $doctrine;
        $this->sortHelper = $sortHelper;
    }

    /**
     * @param int|null $trainTableYearId
     * @param int|null $routeListId
     * @return RoutesDisplay
     */
    public function getRoutesDisplay(?int $trainTableYearId = null, int $routeListId = null): RoutesDisplay
    {
        $routesDisplay = new RoutesDisplay();

        if (is_null($trainTableYearId)) {
            $routesDisplay->trainTableYear = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime());
        } else {
            $routesDisplay->trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->find(
                $trainTableYearId
            );
            if (is_null($routesDisplay->trainTableYear)) {
                throw new AccessDeniedHttpException();
            }

            $routesDisplay->routeLists = $this->doctrine
                ->getRepository(RouteList::class)
                ->findBy(['trainTableYear' => $routesDisplay->trainTableYear], ['firstNumber' => 'ASC']);

            if (!is_null($routeListId)) {
                $routesDisplay->selectedRouteList = $this->doctrine->getRepository(RouteList::class)->find(
                    $routeListId
                );
                if (is_null($routesDisplay->selectedRouteList)) {
                    throw new AccessDeniedHttpException();
                }

                $routes = $routesDisplay->selectedRouteList->getRoutes();
                $routesDisplay->routes = $this->sortHelper->sortByFieldFilter($routes, 'number');
            }
        }

        return $routesDisplay;
    }
}
