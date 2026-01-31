<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\TrainTable;
use App\Entity\TrainTableFirstLast;
use App\Entity\TrainTableYear;
use App\Repository\LocationRepository;
use App\Repository\RouteListRepository;
use App\Repository\RouteOperationDaysRepository;
use App\Repository\TrainTableRepository;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RouteManagementHelper
{
    use DateTrait;

    private const ROUTE_KEY_ROUTE_OPERATION_DAYS = 'routeOperationDays';
    private const ROUTE_KEY_LINES = 'lines';
    private const ROUTE_LINE_KEY_LOCATION = 'location';
    private const ROUTE_LINE_KEY_ACTION = 'action';
    private const ROUTE_LINE_KEY_TIME = 'time';

    private ?RouteList $route_list = null;

    private ?Route $route = null;

    /**
     * @var TrainTable[]
     */
    private array $train_table_lines;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly LocationRepository $location_repository,
        private readonly RouteListRepository $route_list_repository,
        private readonly TrainTableRepository $train_table_repository,
        private readonly RouteOperationDaysRepository $route_operation_days_repository,
    ) {
    }

    public function getRouteList(): RouteList
    {
        return $this->route_list;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    /**
     * @return TrainTable[]
     */
    public function getTrainTableLines(): array
    {
        return $this->train_table_lines;
    }

    public function setRouteListFromId(int $route_list_id): void
    {
        /**
         * @var RouteList|null $route_list
         */
        $route_list = $this->route_list_repository->find($route_list_id);
        if (null === $route_list) {
            throw new AccessDeniedException('This routeList does not exist');
        }
        $this->route_list = $route_list;
    }

    public function setRouteFromId(int $route_id): void
    {
        $route = null;
        if (null !== $route_id && $route_id > 0) {
            /**
             * @var Route|null $route
             */
            $route = $this->doctrine->getRepository(Route::class)->find($route_id);
            if (null === $route) {
                throw new AccessDeniedException('This route does not exist');
            }
        }
        $this->route = $route;
    }

    public function setTrainTableLines(?int $route_number = null): bool
    {
        if (null !== $route_number) {
            // Check if the new route-number is in the correct range of the routeList
            if ($route_number < $this->route_list->first_number || $route_number > $this->route_list->last_number) {
                $route_list = $this->route_list_repository->findForRouteNumber($this->route_list->train_table_year, $route_number);
                if (null === $route_list) {
                    return false;
                }
                // We set this after the negative return, so te original routeList can still be retrieved from
                // the getter to redirect to the correct screen
                $this->route_list = $route_list;
            }

            $this->train_table_lines = $this->train_table_repository->findBy(
                ['train_table_year' => $this->route_list->train_table_year, 'route' => $this->route],
                ['order' => 'ASC']
            );

            $this->route = $this->getNewRouteFromNumber($route_number);

            if (!in_array($this->route_list, $this->route->getRouteLists())) {
                $this->route->addRouteList($this->route_list);
                $this->route_list->addRoute($this->route);
            }
            $this->doctrine->getManager()->persist($this->route);
        }

        if (!isset($this->train_table_lines)) {
            $this->train_table_lines = $this->train_table_repository->findBy(
                ['train_table_year' => $this->route_list->train_table_year, 'route' => $this->route],
                ['order' => 'ASC']
            );
        }

        return true;
    }

    private function getNewRouteFromNumber(int $route_number): Route
    {
        $new_route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $route_number]);
        if (null === $new_route) {
            if (null === $this->route) {
                $new_route = new Route();
            } else {
                $new_route = clone($this->route);
            }
            $new_route->number = (string) $route_number;
        }
        return $new_route;
    }

    public function handlePost(int $route_id, array $submitted_fields): bool
    {
        if ($route_id === 0) {
            if (!\in_array($this->route_list, $this->route->getRouteLists())) {
                $this->route->addRouteList($this->route_list);
                $this->route_list->addRoute($this->route);
            }
            $this->doctrine->getManager()->persist($this->route);
        }

        $this->removeExistingTrainTablesFromRoute($this->route_list->train_table_year, $this->route);
        $route_day_array = $this->getUniqueRouteDayArray($this->getRouteDayArray($submitted_fields));

        return $this->saveRouteDay($route_day_array, $this->route_list->train_table_year, $this->route);
    }

    private function getRouteDayArray(array $submitted_fields): array
    {
        $route_day_array = [];
        foreach ($submitted_fields as $key => $value) {
            $key_part = \explode('_', $key);
            $route_day_array[(int) $key_part[1]][(int) $key_part[2]][$key_part[0]] = $value;
        }

        return $route_day_array;
    }

    private function getUniqueRouteDayArray(array $route_day_array): array
    {
        $result_array = [];
        for ($day_number = 1; $day_number <= 7; ++$day_number) {
            if (isset($route_day_array[$day_number])) {
                $days = $this->getEmptyDaysArray();
                $days[$this->getDayName($day_number - 1)] = true;
                for ($check_day_number = $day_number + 1; $check_day_number <= 7; ++$check_day_number) {
                    if (isset($route_day_array[$check_day_number])
                        && $route_day_array[$day_number] === $route_day_array[$check_day_number]
                    ) {
                        $days[$this->getDayName($check_day_number - 1)] = true;
                        unset($route_day_array[$check_day_number]);
                    }
                }

                $route_operation_days = $this->route_operation_days_repository->findByDaysArray($days);
                $result_array[] = [
                    self::ROUTE_KEY_ROUTE_OPERATION_DAYS => $route_operation_days,
                    self::ROUTE_KEY_LINES => $route_day_array[$day_number]
                ];
            }
        }

        return $result_array;
    }

    private function getEmptyDaysArray(): array
    {
        $result = [];
        for ($day_number = 1; $day_number <= 7; ++$day_number) {
            $result[$this->getDayName($day_number - 1)] = false;
        }
        return $result;
    }

    private function removeExistingTrainTablesFromRoute(TrainTableYear $train_table_year, Route $route): void
    {
        foreach ($route->getTrainTables() as $train_table) {
            if ($train_table_year === $train_table->train_table_year) {
                $this->doctrine->getManager()->remove($train_table);
            }
        }
        foreach ($route->getTrainTableFirstLasts() as $train_table_first_last) {
            if ($train_table_year === $train_table_first_last->train_table_year) {
                $this->doctrine->getManager()->remove($train_table_first_last);
            }
        }

        $this->doctrine->getManager()->flush();
    }

    private function saveRouteDay(array $route_day_array, TrainTableYear $train_table_year, Route $route): bool
    {
        $ok_flag = true;

        foreach ($route_day_array as $route_day) {
            $order = 1;
            foreach ($route_day[self::ROUTE_KEY_LINES] as $route_day_line) {
                $train_table = new TrainTable();
                $train_table->order = $order;
                $train_table->action = $route_day_line[self::ROUTE_LINE_KEY_ACTION];
                $train_table->time = $this->timeDisplayToDatabase($route_day_line[self::ROUTE_LINE_KEY_TIME]);
                $train_table->train_table_year = $train_table_year;
                $train_table->route = $route;
                $train_table->route_operation_days = $route_day[self::ROUTE_KEY_ROUTE_OPERATION_DAYS];
                $train_table->location = $this->findLocation($route_day_line[self::ROUTE_LINE_KEY_LOCATION], $ok_flag);

                $this->doctrine->getManager()->persist($train_table);
                ++$order;
            }

            for ($day_number = 1; $day_number <= 7; ++$day_number) {
                if ($route_day[self::ROUTE_KEY_ROUTE_OPERATION_DAYS]->isRunningOnDay($day_number - 1)) {
                    $last_line = \end($route_day[self::ROUTE_KEY_LINES]);
                    $first_line = \reset($route_day[self::ROUTE_KEY_LINES]);

                    $train_table_first_last = new TrainTableFirstLast();
                    $train_table_first_last->train_table_year = $train_table_year;
                    $train_table_first_last->route = $route;
                    $train_table_first_last->day_number = $day_number;
                    $train_table_first_last->first_location = $this->findLocation($first_line[self::ROUTE_LINE_KEY_LOCATION], $ok_flag);
                    $train_table_first_last->first_action = $first_line[self::ROUTE_LINE_KEY_ACTION];
                    $train_table_first_last->first_time = $this->timeDisplayToDatabase($first_line[self::ROUTE_LINE_KEY_TIME]);
                    $train_table_first_last->last_location = $this->findLocation($last_line[self::ROUTE_LINE_KEY_LOCATION], $ok_flag);
                    $train_table_first_last->last_action = $last_line[self::ROUTE_LINE_KEY_ACTION];
                    $train_table_first_last->last_time = $this->timeDisplayToDatabase($last_line[self::ROUTE_LINE_KEY_TIME]);

                    $this->doctrine->getManager()->persist($train_table_first_last);
                }
            }

            $this->doctrine->getManager()->flush();
        }

        return $ok_flag;
    }

    private function findLocation(string $location_name, bool &$ok_flag): Location
    {
        /**
         * @var Location|null $location
         */
        $location = $this->location_repository->findOneBy(['name' => $location_name]);
        if (null === $location) {
            $location = $this->location_repository->findOneBy(['name' => Location::UNKNOWN_NAME]);
            $ok_flag = false;
        }
        
        return $location;
    }
}
