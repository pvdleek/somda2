<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\RouteOperationDays;
use App\Entity\TrainTableYear;
use App\Generics\RoleGenerics;
use App\Helpers\RouteOperationDaysHelper;
use App\Helpers\RoutesDisplayHelper;
use App\Helpers\TrainTableHelper;
use App\Helpers\UserHelper;
use App\Repository\TrainTableYearRepository;
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
        private readonly RoutesDisplayHelper $routes_display_helper,
        private readonly RouteOperationDaysHelper $days_helper,
        private readonly TrainTableHelper $train_table_helper,
        private readonly UserHelper $user_helper,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
    }

    /**
     * @OA\Parameter(
     *     description="The unique identifier of the train_table_year, 0 for the current train_table_year",
     *     in="path",
     *     name="train_table_year_id",
     *     @OA\Schema(type="integer", default="0"),
     * )
     * @OA\Parameter(description="The route_number", in="path", name="route_number", @OA\Schema(type="integer"))
     * @OA\Response(
     *     response=200,
     *     description="The train-table for the requested route_number",
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
    public function indexAction(int $train_table_year_id, int $route_number): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if ($train_table_year_id === 0) {
            $train_table_year_id = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime())->id;
        }

        $this->train_table_helper->setTrainTableYear($train_table_year_id);
        $this->train_table_helper->setRoute((string) $route_number);
        $train_table_lines = $this->train_table_helper->getTrainTableLines();

        $days_filter = [];
        foreach ($train_table_lines as $train_table_line) {
            if (\array_search($train_table_line->routeOperationDays->id, $days_filter) === false) {
                $days_filter[] = $train_table_line->routeOperationDays->id;
            }
        }
        $days_filter = array_values(array_unique($days_filter));

        $days_legend = [];
        /**
         * @var RouteOperationDays[] $route_operation_days
         */
        $route_operation_days = $this->doctrine->getRepository(RouteOperationDays::class)->findAll();
        foreach ($route_operation_days as $routeOperationDay) {
            $days_legend[$routeOperationDay->id] = $this->days_helper->getDisplay($routeOperationDay, true);
        }

        return $this->handleView(
            $this->view([
                'filters' => ['days' => $days_filter],
                'legend' => ['days' => $days_legend],
                'data' => $train_table_lines,
            ], 200)
        );
    }

    /**
     * @OA\Parameter(
     *     description="The unique identifier of the trainTableYear, 0 for the current trainTableYear",
     *     in="path",
     *     name="train_table_year_id",
     *     @OA\Schema(type="integer", default="0"),
     * )
     * @OA\Parameter(
     *     description="The abbreviation of the location requested, for example Ut",
     *     in="path",
     *     name="location_name",
     *     @OA\Schema(type="string"),
     * )
     * @OA\Parameter(
     *     description="The day-number for which to get the passing-routes",
     *     in="path",
     *     name="day_number",
     *     @OA\Schema(type="integer", enum={1,2,3,4,5,6,7}),
     * )
     * @OA\Parameter(
     *     description="The start-time for the passing-routes: hh:mm, hh.mm or hh",
     *     in="path",
     *     name="start_time",
     *     @OA\Schema(type="string"),
     * )
     * @OA\Parameter(
     *     description="The end-time for the passing-routes: hh:mm, hh.mm or hh",
     *     in="path",
     *     name="end_time",
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
     *                     property="transporter_name",
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
        int $train_table_year_id,
        string $location_name,
        int $day_number,
        string $start_time,
        string $end_time
    ): Response {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if ($train_table_year_id === 0) {
            $train_table_year_id = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime())->id;
        }

        $this->train_table_helper->setTrainTableYear($train_table_year_id);
        $this->train_table_helper->setLocation($location_name);

        $passing_routes = $this->train_table_helper->getPassingRoutes($day_number, $start_time, $end_time);

        if (\count($messages = $this->train_table_helper->getErrorMessages()) > 0) {
            return $this->handleView($this->view(['errors' => $messages], 400));
        }

        foreach ($passing_routes as $index => $passing_route) {
            $passing_routes[$index]['time'] = $this->timeDatabaseToDisplay($passing_route['time']);
        }

        return $this->handleView($this->view(['data' => $passing_routes], 200));
    }

    /**
     * @OA\Parameter(
     *     description="The unique identifier of the train_table_year, 0 for the current train_table_year",
     *     in="path",
     *     name="train_table_year_id",
     *     @OA\Schema(type="integer", default="0"),
     * )
     * @OA\Parameter(
     *     description="The unique identifier of the route_list_id",
     *     in="path",
     *     name="route_list_id",
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
     *                 property="train_table_years",
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=TrainTableYear::class)),
     *             ),
     *             @OA\Property(
     *                 property="routeLists",
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=RouteList::class)),
     *             ),
     *             @OA\Property(
     *                 property="selected_route_list",
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
    public function routeOverviewAction(?int $train_table_year_id = null, ?int $route_list_id = null): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        if ($train_table_year_id === 0) {
            $train_table_year_id = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime())->id;
        }

        $routes_display = $this->routes_display_helper->getRoutesDisplay($train_table_year_id, $route_list_id);

        return $this->handleView(
            $this->view([
                'filters' => [
                    'train_table_years' => $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
                    'route_lists' => $routes_display->route_lists,
                    'selected_route_list' => $routes_display->selected_route_list,
                ],
                'data' => $routes_display->routes,
            ], 200)
        );
    }
}
