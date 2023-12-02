<?php

namespace App\Helpers;

use App\Entity\RouteList;
use App\Model\RoutesDisplay;
use App\Repository\RouteList as RepositoryRouteList;
use App\Repository\TrainTableYear;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Extension\RuntimeExtensionInterface;

class RoutesDisplayHelper implements RuntimeExtensionInterface
{
    use SortTrait;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly RepositoryRouteList $repositoryRouteList,
        private readonly TrainTableYear $repositoryTrainTableYear,
    ) {
    }

    public function getRoutesDisplay(int $trainTableYearId = null, int $routeListId = null): RoutesDisplay
    {
        $routesDisplay = new RoutesDisplay();

        if (\is_null($trainTableYearId) || $trainTableYearId === 0) {
            $routesDisplay->trainTableYear = $this->repositoryTrainTableYear->findTrainTableYearByDate(new \DateTime());
        } else {
            $routesDisplay->trainTableYear = $this->repositoryTrainTableYear->find($trainTableYearId);
            if (\is_null($routesDisplay->trainTableYear)) {
                throw new AccessDeniedException('This trainTableYear does not exist');
            }

            $routesDisplay->routeLists = $this->repositoryRouteList->findForOverview($routesDisplay->trainTableYear);
            if (!\is_null($routeListId)) {
                $routesDisplay->selectedRouteList = $this->doctrine->getRepository(RouteList::class)->find(
                    $routeListId
                );
                if (\is_null($routesDisplay->selectedRouteList)) {
                    throw new AccessDeniedException('This routeList does not exist');
                }

                $routes = $routesDisplay->selectedRouteList->getRoutes();
                $routesDisplay->routes = $this->sortByFieldFilter($routes, 'number');
            }
        }

        return $routesDisplay;
    }
}
