<?php

namespace App\Helpers;

use App\Entity\RouteList;
use App\Model\RoutesDisplay;
use App\Repository\RouteListRepository;
use App\Repository\TrainTableYearRepository;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Extension\RuntimeExtensionInterface;

class RoutesDisplayHelper implements RuntimeExtensionInterface
{
    use SortTrait;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly RouteListRepository $route_list_repository,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
    }

    public function getRoutesDisplay(?int $train_table_year_id = null, ?int $route_list_id = null): RoutesDisplay
    {
        $routes_display = new RoutesDisplay();

        if (null === $train_table_year_id || $train_table_year_id === 0) {
            $routes_display->train_table_year = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime());
        } else {
            $routes_display->train_table_year = $this->train_table_year_repository->find($train_table_year_id);
            if (null === $routes_display->train_table_year) {
                throw new AccessDeniedException('This train_table_year does not exist');
            }

            $routes_display->route_lists = $this->route_list_repository->findForOverview($routes_display->train_table_year);
            if (null !== $route_list_id) {
                $routes_display->selected_route_list = $this->doctrine->getRepository(RouteList::class)->find($route_list_id);
                if (null === $routes_display->selected_route_list) {
                    throw new AccessDeniedException('This route_list does not exist');
                }

                $routes = $routes_display->selected_route_list->getRoutes();
                $routes_display->routes = $this->sortByFieldFilter($routes, 'number');
            }
        }

        return $routes_display;
    }
}
