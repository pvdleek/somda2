<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\RouteOperationDays;
use App\Entity\TrainTable;
use App\Entity\TrainTableFirstLast;
use App\Entity\TrainTableYear;
use App\Helpers\DateHelper;
use App\Helpers\FlashHelper;
use App\Helpers\FormHelper;
use App\Helpers\SortHelper;
use App\Helpers\TemplateHelper;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ManageTrainTablesController
{
    use DateTrait;

    private const ROUTE_KEY_ROUTE_OPERATION_DAYS = 'routeOperationDays';
    private const ROUTE_KEY_LINES = 'lines';
    private const ROUTE_LINE_KEY_LOCATION = 'location';
    private const ROUTE_LINE_KEY_ACTION = 'action';
    private const ROUTE_LINE_KEY_TIME = 'time';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;

    /**
     * @var DateHelper
     */
    private DateHelper $dateHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     * @param SortHelper $sortHelper
     * @param DateHelper $dateHelper
     */
    public function __construct(ManagerRegistry $doctrine, FormHelper $formHelper, TemplateHelper $templateHelper, SortHelper $sortHelper, DateHelper $dateHelper)
    {
        $this->doctrine = $doctrine;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
        $this->sortHelper = $sortHelper;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param int|null $yearId
     * @param int|null $routeListId
     * @return Response
     */
    public function manageAction(int $yearId = null, int $routeListId = null): Response
    {
        $routeLists = [];
        $selectedRouteList = null;
        $routes = [];

        /**
         * @var TrainTableYear $selectedTrainTableYear
         * @var RouteList $selectedRouteList
         */
        if (is_null($yearId)) {
            $selectedTrainTableYear = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findCurrentTrainTableYear();
        } else {
            $selectedTrainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->find($yearId);
            if (is_null($selectedTrainTableYear)) {
                throw new AccessDeniedHttpException();
            }

            $routeLists = $this->doctrine
                ->getRepository(RouteList::class)
                ->findBy(['trainTableYear' => $selectedTrainTableYear], ['firstNumber' => 'ASC']);

            if (!is_null($routeListId)) {
                $selectedRouteList = $this->doctrine->getRepository(RouteList::class)->find($routeListId);
                if (is_null($selectedRouteList)) {
                    throw new AccessDeniedHttpException();
                }

                $routes = $selectedRouteList->getRoutes();
                $routes = $this->sortHelper->sortByFieldFilter($routes, 'number');
            }
        }

        return $this->templateHelper->render('manageTrainTables/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregelingen',
            'trainTableYears' => $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findBy([], ['startDate' => 'DESC']),
            'selectedTrainTableYear' => $selectedTrainTableYear,
            'routeLists' => $routeLists,
            'selectedRouteList' => $selectedRouteList,
            'routes' => $routes,
        ]);
    }

    /**
     * @param Request $request
     * @param int $routeListId
     * @param int $routeId
     * @return RedirectResponse|Response
     */
    public function manageRouteAction(Request $request, int $routeListId, int $routeId)
    {
        /**
         * @var RouteList $routeList
         */
        $routeList = $this->doctrine->getRepository(RouteList::class)->find($routeListId);
        if (is_null($routeList)) {
            throw new AccessDeniedHttpException();
        }

        /**
         * @var Route $route
         */
        $route = $this->doctrine->getRepository(Route::class)->find($routeId);
        if (is_null($route)) {
            throw new AccessDeniedHttpException();
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $routeDayArray = [];
            $allSubmitted = $request->request->all();
            foreach ($allSubmitted as $key => $value) {
                $keyPart = explode('_', $key);
                $routeDayArray[(int)$keyPart[1]][(int)$keyPart[2]][$keyPart[0]] = $value;
            }




            // FILTER!




            $routeDayArray = $this->getUniqueRouteDayArray($routeDayArray);

            $this->removeExistingTrainTablesFromRoute($route);
            if ($this->saveRouteDay($routeDayArray, $routeList->trainTableYear, $route)) {
                return $this->formHelper->finishFormHandling(
                    'Trein opgeslagen',
                    'manage_train_tables_year_route_list',
                    ['yearId' => $routeList->trainTableYear->getId(), 'routeListId' => $routeList->getId()]
                );
            }

            $this->formHelper->getFlashHelper()->add(
                FlashHelper::FLASH_TYPE_ERROR,
                '1 Of meer locaties konden niet worden gevonden, corrigeer aub en sla opnieuw op'
            );
        }

        $trainTableLines = $this->doctrine->getRepository(TrainTable::class)->findBy(
            ['trainTableYear' => $routeList->trainTableYear, 'route' => $route],
            ['order' => 'ASC']
        );

        return $this->templateHelper->render('manageTrainTables/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer dienstregeling',
            'routeList' => $routeList,
            'route' => $route,
            'trainTableLines' => $trainTableLines,
        ]);
    }

    /**
     * @param array $routeDayArray
     * @return array
     */
    private function getUniqueRouteDayArray(array $routeDayArray): array
    {
        $resultArray = [];
        for ($dayNumber = 1; $dayNumber <= 7; ++$dayNumber) {
            if (isset($routeDayArray[$dayNumber])) {
                $days = $this->getEmptyDaysArray();
                $days[$this->dateHelper->getDayName($dayNumber - 1)] = true;
                for ($checkDayNumber = $dayNumber + 1; $checkDayNumber <= 7; ++$checkDayNumber) {
                    if (isset($routeDayArray[$checkDayNumber])
                        && $routeDayArray[$dayNumber] === $routeDayArray[$checkDayNumber]
                    ) {
                        $days[$this->dateHelper->getDayName($checkDayNumber - 1)] = true;
                        unset($routeDayArray[$checkDayNumber]);
                    }
                }

                $routeOperationDays = $this->doctrine
                    ->getRepository(RouteOperationDays::class)
                    ->findByDaysArray($days);
                $resultArray[] = [
                    self::ROUTE_KEY_ROUTE_OPERATION_DAYS => $routeOperationDays,
                    self::ROUTE_KEY_LINES => $routeDayArray[$dayNumber]
                ];
            }
        }

        return $resultArray;
    }

    /**
     * @return array
     */
    private function getEmptyDaysArray(): array
    {
        $result = [];
        for ($dayNumber = 1; $dayNumber <= 7; ++$dayNumber) {
            $result[$this->dateHelper->getDayName($dayNumber - 1)] = false;
        }
        return $result;
    }

    /**
     * @param Route $route
     */
    private function removeExistingTrainTablesFromRoute(Route $route): void
    {
        foreach ($route->getTrainTables() as $trainTable) {
            $this->doctrine->getManager()->remove($trainTable);
        }
        foreach ($route->getTrainTableFirstLasts() as $trainTableFirstLast) {
            $this->doctrine->getManager()->remove($trainTableFirstLast);
        }

        $this->doctrine->getManager()->flush();
    }

    /**
     * @param array $routeDayArray
     * @param TrainTableYear $trainTableYear
     * @param Route $route
     * @return bool
     */
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
                if ($routeDay[self::ROUTE_KEY_ROUTE_OPERATION_DAYS]->getDay($dayNumber - 1)) {
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

    /**
     * @param string $locationName
     * @param bool $okFlag
     * @return Location
     */
    private function findLocation(string $locationName, bool &$okFlag): Location
    {
        /**
         * @var Location $location
         */
        $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $locationName]);
        if (is_null($location)) {
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => Location::UNKNOWN_NAME]);
            $okFlag = false;
        }
        return $location;
    }
}
