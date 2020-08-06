<?php

namespace App\Controller\Api;

use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\RouteOperationDays;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Helpers\Controller\TrainTableHelper;
use App\Helpers\RouteOperationDaysHelper;
use App\Helpers\RoutesDisplayHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class TrainTableController extends AbstractFOSRestController
{
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
    private RouteOperationDaysHelper $routeOperationDaysHelper;

    /**
     * @var RoutesDisplayHelper
     */
    private RoutesDisplayHelper $routesDisplayHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TrainTableHelper $trainTableHelper
     * @param RouteOperationDaysHelper $routeOperationDaysHelper
     * @param RoutesDisplayHelper $routesDisplayHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        TrainTableHelper $trainTableHelper,
        RouteOperationDaysHelper $routeOperationDaysHelper,
        RoutesDisplayHelper $routesDisplayHelper
    ) {
        $this->doctrine = $doctrine;
        $this->trainTableHelper = $trainTableHelper;
        $this->routeOperationDaysHelper = $routeOperationDaysHelper;
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

        $days = [];
        foreach ($trainTableLines as $trainTableLine) {
            if (!isset($routeOperationDaysIdArray[$trainTableLine->routeOperationDays->getId()])) {
                $days[] = $trainTableLine->routeOperationDays->getId();
            }
        }

        $routeOperationDaysArray = [];
        /**
         * @var RouteOperationDays[] $routeOperationDays
         */
        $routeOperationDays = $this->doctrine->getRepository(RouteOperationDays::class)->findAll();
        foreach ($routeOperationDays as $routeOperationDay) {
            $routeOperationDaysArray[$routeOperationDay->getId()] =
                $this->routeOperationDaysHelper->getDisplay($routeOperationDay, true);
        }

        return $this->handleView(
            $this->view([
                'filters' => ['days' => array_values(array_unique($days))],
                'legend' => ['days' => $routeOperationDaysArray],
                'data' => $trainTableLines,
            ], 200)
        );
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
