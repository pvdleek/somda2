<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\TrainTable;
use App\Entity\TrainTableFirstLast;
use App\Entity\TrainTableYear;
use App\Repository\Location as RepositoryLocation;
use App\Repository\RouteList as RepositoryRouteList;
use App\Repository\RouteOperationDays;
use App\Repository\TrainTable as RepositoryTrainTable;
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

    private ?RouteList $routeList = null;

    private ?Route $route = null;

    /**
     * @var TrainTable[]
     */
    private array $trainTableLines;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly RepositoryLocation $repositoryLocation,
        private readonly RepositoryRouteList $repositoryRouteList,
        private readonly RepositoryTrainTable $repositoryTrainTable,
        private readonly RouteOperationDays $repositoryRouteOperationDays,
    ) {
    }

    public function getRouteList(): RouteList
    {
        return $this->routeList;
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
        return $this->trainTableLines;
    }

    public function setRouteListFromId(int $routeListId): void
    {
        /**
         * @var RouteList $routeList
         */
        $routeList = $this->repositoryRouteList->find($routeListId);
        if (\is_null($routeList)) {
            throw new AccessDeniedException('This routeList does not exist');
        }
        $this->routeList = $routeList;
    }

    public function setRouteFromId(int $routeId): void
    {
        $route = null;
        if (!\is_null($routeId) && $routeId > 0) {
            /**
             * @var Route $route
             */
            $route = $this->doctrine->getRepository(Route::class)->find($routeId);
            if (\is_null($route)) {
                throw new AccessDeniedException('This route does not exist');
            }
        }
        $this->route = $route;
    }

    public function setTrainTableLines(?int $routeNumber = null): bool
    {
        if (!\is_null($routeNumber)) {
            // Check if the new route-number is in the correct range of the routeList
            if ($routeNumber < $this->routeList->firstNumber || $routeNumber > $this->routeList->lastNumber) {
                $routeList = $this->repositoryRouteList->findForRouteNumber($this->routeList->trainTableYear, $routeNumber);
                if (\is_null($routeList)) {
                    return false;
                }
                // We set this after the negative return, so te original routeList can still be retrieved from
                // the getter to redirect to the correct screen
                $this->routeList = $routeList;
            }

            $this->trainTableLines = $this->repositoryTrainTable->findBy(
                ['trainTableYear' => $this->routeList->trainTableYear, 'route' => $this->route],
                ['order' => 'ASC']
            );

            $this->route = $this->getNewRouteFromNumber($routeNumber);

            if (!in_array($this->routeList, $this->route->getRouteLists())) {
                $this->route->addRouteList($this->routeList);
                $this->routeList->addRoute($this->route);
            }
            $this->doctrine->getManager()->persist($this->route);
        }

        if (!isset($this->trainTableLines)) {
            $this->trainTableLines = $this->repositoryTrainTable->findBy(
                ['trainTableYear' => $this->routeList->trainTableYear, 'route' => $this->route],
                ['order' => 'ASC']
            );
        }

        return true;
    }

    private function getNewRouteFromNumber(int $routeNumber): Route
    {
        $newRoute = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $routeNumber]);
        if (\is_null($newRoute)) {
            if (\is_null($this->route)) {
                $newRoute = new Route();
            } else {
                $newRoute = clone($this->route);
            }
            $newRoute->number = (string) $routeNumber;
        }
        return $newRoute;
    }

    public function handlePost(int $routeId, array $submittedFields): bool
    {
        if ($routeId === 0) {
            if (!\in_array($this->routeList, $this->route->getRouteLists())) {
                $this->route->addRouteList($this->routeList);
                $this->routeList->addRoute($this->route);
            }
            $this->doctrine->getManager()->persist($this->route);
        }

        $this->removeExistingTrainTablesFromRoute($this->routeList->trainTableYear, $this->route);
        $routeDayArray = $this->getUniqueRouteDayArray($this->getRouteDayArray($submittedFields));
        return $this->saveRouteDay($routeDayArray, $this->routeList->trainTableYear, $this->route);
    }

    private function getRouteDayArray(array $submittedFields): array
    {
        $routeDayArray = [];
        foreach ($submittedFields as $key => $value) {
            $keyPart = \explode('_', $key);
            $routeDayArray[(int) $keyPart[1]][(int) $keyPart[2]][$keyPart[0]] = $value;
        }
        return $routeDayArray;
    }

    private function getUniqueRouteDayArray(array $routeDayArray): array
    {
        $resultArray = [];
        for ($dayNumber = 1; $dayNumber <= 7; ++$dayNumber) {
            if (isset($routeDayArray[$dayNumber])) {
                $days = $this->getEmptyDaysArray();
                $days[$this->getDayName($dayNumber - 1)] = true;
                for ($checkDayNumber = $dayNumber + 1; $checkDayNumber <= 7; ++$checkDayNumber) {
                    if (isset($routeDayArray[$checkDayNumber])
                        && $routeDayArray[$dayNumber] === $routeDayArray[$checkDayNumber]
                    ) {
                        $days[$this->getDayName($checkDayNumber - 1)] = true;
                        unset($routeDayArray[$checkDayNumber]);
                    }
                }

                $routeOperationDays = $this->repositoryRouteOperationDays->findByDaysArray($days);
                $resultArray[] = [
                    self::ROUTE_KEY_ROUTE_OPERATION_DAYS => $routeOperationDays,
                    self::ROUTE_KEY_LINES => $routeDayArray[$dayNumber]
                ];
            }
        }

        return $resultArray;
    }

    private function getEmptyDaysArray(): array
    {
        $result = [];
        for ($dayNumber = 1; $dayNumber <= 7; ++$dayNumber) {
            $result[$this->getDayName($dayNumber - 1)] = false;
        }
        return $result;
    }

    private function removeExistingTrainTablesFromRoute(TrainTableYear $trainTableYear, Route $route): void
    {
        foreach ($route->getTrainTables() as $trainTable) {
            if ($trainTableYear === $trainTable->trainTableYear) {
                $this->doctrine->getManager()->remove($trainTable);
            }
        }
        foreach ($route->getTrainTableFirstLasts() as $trainTableFirstLast) {
            if ($trainTableYear === $trainTableFirstLast->trainTableYear) {
                $this->doctrine->getManager()->remove($trainTableFirstLast);
            }
        }

        $this->doctrine->getManager()->flush();
    }

    private function saveRouteDay(array $routeDayArray, TrainTableYear $trainTableYear, Route $route): bool
    {
        $okFlag = true;

        foreach ($routeDayArray as $routeDay) {
            $order = 1;
            foreach ($routeDay[self::ROUTE_KEY_LINES] as $routeDayLine) {
                $trainTable = new TrainTable();
                $trainTable->order = $order;
                $trainTable->action = $routeDayLine[self::ROUTE_LINE_KEY_ACTION];
                $trainTable->time = $this->timeDisplayToDatabase($routeDayLine[self::ROUTE_LINE_KEY_TIME]);
                $trainTable->trainTableYear = $trainTableYear;
                $trainTable->route = $route;
                $trainTable->routeOperationDays = $routeDay[self::ROUTE_KEY_ROUTE_OPERATION_DAYS];
                $trainTable->location = $this->findLocation($routeDayLine[self::ROUTE_LINE_KEY_LOCATION], $okFlag);

                $this->doctrine->getManager()->persist($trainTable);
                ++$order;
            }

            for ($dayNumber = 1; $dayNumber <= 7; ++$dayNumber) {
                if ($routeDay[self::ROUTE_KEY_ROUTE_OPERATION_DAYS]->isRunningOnDay($dayNumber - 1)) {
                    $lastLine = end($routeDay[self::ROUTE_KEY_LINES]);
                    $firstLine = reset($routeDay[self::ROUTE_KEY_LINES]);

                    $trainTableFirstLast = new TrainTableFirstLast();
                    $trainTableFirstLast->trainTableYear = $trainTableYear;
                    $trainTableFirstLast->route = $route;
                    $trainTableFirstLast->dayNumber = $dayNumber;
                    $trainTableFirstLast->firstLocation =
                        $this->findLocation($firstLine[self::ROUTE_LINE_KEY_LOCATION], $okFlag);
                    $trainTableFirstLast->firstAction = $firstLine[self::ROUTE_LINE_KEY_ACTION];
                    $trainTableFirstLast->firstTime =
                        $this->timeDisplayToDatabase($firstLine[self::ROUTE_LINE_KEY_TIME]);
                    $trainTableFirstLast->lastLocation =
                        $this->findLocation($lastLine[self::ROUTE_LINE_KEY_LOCATION], $okFlag);
                    $trainTableFirstLast->lastAction = $lastLine[self::ROUTE_LINE_KEY_ACTION];
                    $trainTableFirstLast->lastTime = $this->timeDisplayToDatabase($lastLine[self::ROUTE_LINE_KEY_TIME]);

                    $this->doctrine->getManager()->persist($trainTableFirstLast);
                }
            }

            $this->doctrine->getManager()->flush();
        }

        return $okFlag;
    }

    private function findLocation(string $locationName, bool &$okFlag): Location
    {
        /**
         * @var Location $location
         */
        $location = $this->repositoryLocation->findOneBy(['name' => $locationName]);
        if (\is_null($location)) {
            $location = $this->repositoryLocation->findOneBy(['name' => Location::UNKNOWN_NAME]);
            $okFlag = false;
        }
        return $location;
    }
}
