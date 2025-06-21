<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\RouteOperationDays;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Generics\RoleGenerics;
use App\Helpers\RouteOperationDaysHelper;
use App\Helpers\RoutesDisplayHelper;
use App\Helpers\TrainTableHelper;
use App\Helpers\UserHelper;
use App\Traits\DateTrait;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class TrainTableController extends AbstractFOSRestController
{
    use DateTrait;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TrainTableHelper $trainTableHelper,
        private readonly RouteOperationDaysHelper $daysHelper,
        private readonly RoutesDisplayHelper $routesDisplayHelper,
    ) {
    }

    /**
     * @OA\Parameter(
     *     description="The unique identifier of the trainTableYear, 0 for the current trainTableYear",
     *     in="path",
     *     name="trainTableYearId",
     *     @OA\Schema(type="integer", default="0"),
     * )
     * @OA\Parameter(description="The routeNumber", in="path", name="routeNumber", @OA\Schema(type="integer"))
     * @OA\Response(
     *     response=200,
     *     description="The train-table for the requested routeNumber",
     *     @OA\Schema(
     *         @OA\Property(
     *             property="filters",
     *             type="object",
     *             @OA\Property(property="days", type="array", @OA\Items(type="integer")),
     *         ),
     *         @OA\Property(
     *             property="legend",
     *             type="object",
     *             @OA\Property(
     *                 property="days",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="The day-identification (integer) as defined in the filters property",
     *                         description="Visual representation of the days the route runs",
     *                         type="string"
     *                     ),
     *                 ),
     *             ),
     *         ),
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=TrainTable::class))),
     *     ),
     * )
     * @OA\Tag(name="Train-tables")
     */
    public function indexAction(int $trainTableYearId, int $routeNumber): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if ($trainTableYearId === 0) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new \DateTime())
                ->id;
        }

        $this->trainTableHelper->setTrainTableYear($trainTableYearId);
        $this->trainTableHelper->setRoute((string) $routeNumber);
        $trainTableLines = $this->trainTableHelper->getTrainTableLines();

        $daysFilter = [];
        foreach ($trainTableLines as $trainTableLine) {
            if (\array_search($trainTableLine->routeOperationDays->id, $daysFilter) === false) {
                $daysFilter[] = $trainTableLine->routeOperationDays->id;
            }
        }
        $daysFilter = array_values(array_unique($daysFilter));

        $daysLegend = [];
        /**
         * @var RouteOperationDays[] $routeOperationDays
         */
        $routeOperationDays = $this->doctrine->getRepository(RouteOperationDays::class)->findAll();
        foreach ($routeOperationDays as $routeOperationDay) {
            $daysLegend[$routeOperationDay->id] = $this->daysHelper->getDisplay($routeOperationDay, true);
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
     * @OA\Parameter(
     *     description="The unique identifier of the trainTableYear, 0 for the current trainTableYear",
     *     in="path",
     *     name="trainTableYearId",
     *     @OA\Schema(type="integer", default="0"),
     * )
     * @OA\Parameter(
     *     description="The abbreviation of the location requested, for example Ut",
     *     in="path",
     *     name="locationName",
     *     @OA\Schema(type="string"),
     * )
     * @OA\Parameter(
     *     description="The day-number for which to get the passing-routes",
     *     in="path",
     *     name="dayNumber",
     *     @OA\Schema(type="integer", enum={1,2,3,4,5,6,7}),
     * )
     * @OA\Parameter(
     *     description="The start-time for the passing-routes: hh:mm, hh.mm or hh",
     *     in="path",
     *     name="startTime",
     *     @OA\Schema(type="string"),
     * )
     * @OA\Parameter(
     *     description="The end-time for the passing-routes: hh:mm, hh.mm or hh",
     *     in="path",
     *     name="endTime",
     *     @OA\Schema(type="string"),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Overview of passing routes for a location",
     *     @OA\Schema(
     *         @OA\Property(
     *             property="data",
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(
     *                     description="The time of the action (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *                     property="time",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The action of the route: 'v' for departure, '-' for a drivethrough,\
                                        '+' for a short stop and 'a' for arrival",
     *                     enum={"v","-","+","a"},
     *                     maxLength=1,
     *                     property="action",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The route-number",
     *                     maxLength=15,
     *                     property="route_number",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The abbreviation of the first location of this route",
     *                     maxLength=10,
     *                     property="fl_first_name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The description of the first location of this route",
     *                     maxLength=100,
     *                     property="fl_first_description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The abbreviation of the last location of this route",
     *                     maxLength=10,
     *                     property="fl_last_name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The description of the last location of this route",
     *                     maxLength=100,
     *                     property="fl_last_description",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The name of the transporter of this route",
     *                     maxLength=35,
     *                     property="transporterName",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The name of the characteristic of this route",
     *                     maxLength=5,
     *                     property="characteristicName",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     description="The description of the characteristic of this route",
     *                     maxLength=25,
     *                     property="characteristicDescription",
     *                     type="string",
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     * @OA\Response(
     *     response=400,
     *     description="The request failed",
     *     @OA\Schema(@OA\Property(property="errors", type="array", @OA\Items(type="string"))),
     * )
     * @OA\Tag(name="Train-tables")
     */
    public function passingRoutesAction(
        int $trainTableYearId,
        string $locationName,
        int $dayNumber,
        string $startTime,
        string $endTime
    ): Response {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if ($trainTableYearId === 0) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new \DateTime())
                ->id;
        }

        $this->trainTableHelper->setTrainTableYear($trainTableYearId);
        $this->trainTableHelper->setLocation($locationName);

        $passingRoutes = $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime, $endTime);

        if (\count($messages = $this->trainTableHelper->getErrorMessages()) > 0) {
            return $this->handleView($this->view(['errors' => $messages], 400));
        }

        foreach ($passingRoutes as $index => $passingRoute) {
            $passingRoutes[$index]['time'] = $this->timeDatabaseToDisplay($passingRoute['time']);
        }

        return $this->handleView($this->view(['data' => $passingRoutes], 200));
    }

    /**
     * @OA\Parameter(
     *     description="The unique identifier of the trainTableYear, 0 for the current trainTableYear",
     *     in="path",
     *     name="trainTableYearId",
     *     @OA\Schema(type="integer", default="0"),
     * )
     * @OA\Parameter(
     *     description="The unique identifier of the routeListId",
     *     in="path",
     *     name="routeListId",
     *     @OA\Schema(type="integer"),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Overview of routes in the requested trainTableYear and routeList",
     *     @OA\Schema(
     *         @OA\Property(
     *             property="filters",
     *             type="object",
     *             @OA\Property(
     *                 property="trainTableYears",
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=TrainTableYear::class)),
     *             ),
     *             @OA\Property(
     *                 property="routeLists",
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=RouteList::class)),
     *             ),
     *             @OA\Property(
     *                 property="selectedRouteList",
     *                 ref=@Model(type=RouteList::class),
     *                 type="object",
     *             ),
     *         ),
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=Route::class))),
     *     ),
     * )
     * @OA\Response(
     *     response=403,
     *     description="The request failed",
     *     @OA\Schema(@OA\Property(description="Description of the error", property="error", type="string")),
     * )
     * @OA\Tag(name="Train-tables")
     */
    public function routeOverviewAction(?int $trainTableYearId = null, ?int $routeListId = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if ($trainTableYearId === 0) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new \DateTime())
                ->id;
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
