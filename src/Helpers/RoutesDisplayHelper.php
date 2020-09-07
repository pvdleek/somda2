<?php

namespace App\Helpers;

use App\Entity\RouteList;
use App\Entity\TrainTableYear;
use App\Model\RoutesDisplay;
use App\Traits\SortTrait;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Extension\RuntimeExtensionInterface;

class RoutesDisplayHelper implements RuntimeExtensionInterface
{
    use SortTrait;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param int|null $trainTableYearId
     * @param int|null $routeListId
     * @return RoutesDisplay
     */
    public function getRoutesDisplay(int $trainTableYearId = null, int $routeListId = null): RoutesDisplay
    {
        $routesDisplay = new RoutesDisplay();

        if (is_null($trainTableYearId) || $trainTableYearId === 0) {
            $routesDisplay->trainTableYear = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime());
        } else {
            $routesDisplay->trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->find(
                $trainTableYearId
            );
            if (is_null($routesDisplay->trainTableYear)) {
                throw new AccessDeniedException('This trainTableYear does not exist');
            }

            $routesDisplay->routeLists = $this->doctrine
                ->getRepository(RouteList::class)
                ->findForOverview($routesDisplay->trainTableYear);

            if (!is_null($routeListId)) {
                $routesDisplay->selectedRouteList = $this->doctrine->getRepository(RouteList::class)->find(
                    $routeListId
                );
                if (is_null($routesDisplay->selectedRouteList)) {
                    throw new AccessDeniedException("This routeList does not exist");
                }

                $routes = $routesDisplay->selectedRouteList->getRoutes();
                $routesDisplay->routes = $this->sortByFieldFilter($routes, 'number');
            }
        }

        return $routesDisplay;
    }
}
