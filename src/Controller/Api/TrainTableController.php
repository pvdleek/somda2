<?php

namespace App\Controller\Api;

use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\RouteOperationDays;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Helpers\RouteOperationDaysHelper;
use App\Helpers\RoutesDisplayHelper;
use App\Helpers\TrainTableHelper;
use App\Traits\DateTrait;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class TrainTableController extends AbstractFOSRestController
{
    use DateTrait;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var TrainTableHelper
     */
    private TrainTableHelper $trainTableHelper;

    /**
     * @var RouteOperationDaysHelper
     */
    private RouteOperationDaysHelper $daysHelper;

    /**
     * @var RoutesDisplayHelper
     */
    private RoutesDisplayHelper $routesDisplayHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TrainTableHelper $trainTableHelper
     * @param RouteOperationDaysHelper $daysHelper
     * @param RoutesDisplayHelper $routesDisplayHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        TrainTableHelper $trainTableHelper,
        RouteOperationDaysHelper $daysHelper,
        RoutesDisplayHelper $routesDisplayHelper
    ) {
        $this->doctrine = $doctrine;
        $this->trainTableHelper = $trainTableHelper;
        $this->daysHelper = $daysHelper;
        $this->routesDisplayHelper = $routesDisplayHelper;
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int $trainTableYearId
     * @param int $routeNumber
     * @return Response
     * @SWG\Parameter(
     *     default="0",
     *     description="The unique identifier of the trainTableYear, 0 for the current trainTableYear",
     *     in="path",
     *     name="trainTableYearId",
     *     type="integer",
     * )
     * @SWG\Parameter(description="The routeNumber", in="path", name="routeNumber", type="integer")
     * @SWG\Response(
     *     response=200,
     *     description="The train-table for the requested routeNumber",
     *     @SWG\Schema(
     *         @SWG\Property(
     *             property="filters",
     *             type="object",
     *             @SWG\Property(property="days", type="array", @SWG\Items(type="integer")),
     *         ),
     *         @SWG\Property(
     *             property="legend",
     *             type="object",
     *             @SWG\Property(
     *                 property="days",
     *                 type="array",
     *                 @SWG\Items(
     *                     @SWG\Property(
     *                         property="The day-identification (integer) as defined in the filters property",
     *                         description="Visual representation of the days the route runs",
     *                         type="string"
     *                     ),
     *                 ),
     *             ),
     *         ),
     *         @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=TrainTable::class))),
     *     ),
     * )
     * @SWG\Tag(name="Train-tables")
     */
    public function indexAction(int $trainTableYearId, int $routeNumber): Response
    {
        if ($trainTableYearId === 0) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime())
                ->getId();
        }

        $this->trainTableHelper->setTrainTableYear($trainTableYearId);
        $this->trainTableHelper->setRoute($routeNumber);
        $trainTableLines = $this->trainTableHelper->getTrainTableLines();

        $daysFilter = [];
        foreach ($trainTableLines as $trainTableLine) {
            if (array_search($trainTableLine->routeOperationDays->getId(), $daysFilter) === false) {
                $daysFilter[] = $trainTableLine->routeOperationDays->getId();
            }
        }
        $daysFilter = array_values(array_unique($daysFilter));

        $daysLegend = [];
        /**
         * @var RouteOperationDays[] $routeOperationDays
         */
        $routeOperationDays = $this->doctrine->getRepository(RouteOperationDays::class)->findAll();
        foreach ($routeOperationDays as $routeOperationDay) {
            $daysLegend[$routeOperationDay->getId()] =
                $this->daysHelper->getDisplay($routeOperationDay, true);
        }

        return $this->handleView(
            $this->view([
                'filters' => ['days' => $daysFilter],
                'legend' => ['days' => $daysLegend],
                'data' => $trainTableLines,
            ], 200)
        );
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int $trainTableYearId
     * @param string $locationName
     * @param int $dayNumber
     * @param string $startTime
     * @param string $endTime
     * @return Response
     * @SWG\Parameter(
     *     default="0",
     *     description="The unique identifier of the trainTableYear, 0 for the current trainTableYear",
     *     in="path",
     *     name="trainTableYearId",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     description="The abbreviation of the location requested, for example Ut",
     *     in="path",
     *     name="locationName",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     description="The day-number for which to get the passing-routes",
     *     enum={1,2,3,4,5,6,7},
     *     in="path",
     *     name="dayNumber",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     description="The start-time for the passing-routes: hh:mm, hh.mm or hh",
     *     in="path",
     *     name="startTime",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     description="The end-time for the passing-routes: hh:mm, hh.mm or hh",
     *     in="path",
     *     name="endTime",
     *     type="string",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Overview of passing routes for a location",
     *     @SWG\Schema(
     *         @SWG\Property(
     *             property="data",
     *             type="array",
     *             @SWG\Items(
     *                 @SWG\Property(
     *                     description="The time of the action (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *                     property="time",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The action of the route: 'v' for departure, '-' for a drivethrough,\
                                        '+' for a short stop and 'a' for arrival",
     *                     maxLength=1,
     *                     enum={"v","-","+","a"},
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The route-number",
     *                     maxLength=15,
     *                     property="route_number",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The abbreviation of the first location of this route",
     *                     maxLength=10,
     *                     property="fl_first_name",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The description of the first location of this route",
     *                     maxLength=100,
     *                     property="fl_first_description",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The abbreviation of the last location of this route",
     *                     maxLength=10,
     *                     property="fl_last_name",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The description of the last location of this route",
     *                     maxLength=100,
     *                     property="fl_last_description",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The name of the transporter of this route",
     *                     maxLength=35,
     *                     property="transporterName",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The name of the characteristic of this route",
     *                     maxLength=5,
     *                     property="characteristicName",
     *                     type="string",
     *                 ),
     *                 @SWG\Property(
     *                     description="The description of the characteristic of this route",
     *                     maxLength=25,
     *                     property="characteristicDescription",
     *                     type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     * @SWG\Response(
     *     response=400,
     *     description="The request failed",
     *     @SWG\Schema(@SWG\Property(property="errors", type="array", @SWG\Items(type="string"))),
     * )
     * @SWG\Tag(name="Train-tables")
     */
    public function passingRoutesAction(
        int $trainTableYearId,
        string $locationName,
        int $dayNumber,
        string $startTime,
        string $endTime
    ): Response {
        if ($trainTableYearId === 0) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime())
                ->getId();
        }

        $this->trainTableHelper->setTrainTableYear($trainTableYearId);
        $this->trainTableHelper->setLocation($locationName);

        $passingRoutes = $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime, $endTime);

        if (count($messages = $this->trainTableHelper->getErrorMessages()) > 0) {
            return $this->handleView($this->view(['errors' => $messages], 400));
        }

        foreach ($passingRoutes as $index => $passingRoute) {
            $passingRoutes[$index]['time'] = $this->timeDatabaseToDisplay($passingRoute['time']);
        }

        return $this->handleView($this->view(['data' => $passingRoutes], 200));
    }

    /**
     * @IsGranted("ROLE_API_USER")
     * @param int|null $trainTableYearId
     * @param int|null $routeListId
     * @return Response
     * @SWG\Parameter(
     *     default="0",
     *     description="The unique identifier of the trainTableYear, 0 for the current trainTableYear",
     *     in="path",
     *     name="trainTableYearId",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     description="The unique identifier of the routeListId",
     *     in="path",
     *     name="routeListId",
     *     type="integer",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Overview of routes in the requested trainTableYear and routeList",
     *     @SWG\Schema(
     *         @SWG\Property(
     *             property="filters",
     *             type="object",
     *             @SWG\Property(
     *                 property="trainTableYears",
     *                 type="array",
     *                 @SWG\Items(ref=@Model(type=TrainTableYear::class)),
     *             ),
     *             @SWG\Property(
     *                 property="routeLists",
     *                 type="array",
     *                 @SWG\Items(ref=@Model(type=RouteList::class)),
     *             ),
     *             @SWG\Property(
     *                 property="selectedRouteList",
     *                 ref=@Model(type=RouteList::class),
     *                 type="object",
     *             ),
     *         ),
     *         @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=Route::class))),
     *     ),
     * )
     * @SWG\Response(
     *     response=403,
     *     description="The request failed",
     *     @SWG\Schema(@SWG\Property(description="Description of the error", property="error", type="string")),
     * )
     * @SWG\Tag(name="Train-tables")
     */
    public function routeOverviewAction(int $trainTableYearId = null, int $routeListId = null): Response
    {
        if ($trainTableYearId === 0) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime())
                ->getId();
        }

        $routesDisplay = $this->routesDisplayHelper->getRoutesDisplay($trainTableYearId, $routeListId);

        return $this->handleView(
            $this->view([
                'filters' => [
                    'trainTableYears' => $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
                    'routeLists' => $routesDisplay->routeLists,
                    'selectedRouteList' => $routesDisplay->selectedRouteList,
                ],
                'data' => $routesDisplay->routes,
            ], 200)
        );
    }
}
