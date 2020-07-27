<?php

namespace App\Controller\Api;

use App\Entity\RouteOperationDays;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Helpers\Controller\TrainTableHelper;
use App\Helpers\RouteOperationDaysHelper;
use App\Helpers\RoutesDisplayHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
     * @throws Exception
     * @SWG\Parameter(
     *     default="Provide 0 to get the current trainTableYear",
     *     description="The unique identifier of the trainTableYear",
     *     in="path",
     *     name="trainTableYearId",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     description="The routeNumber",
     *     in="path",
     *     name="routeNumber",
     *     type="integer",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the trainTable for a specific routeNumber",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TrainTable::class))
     *     )
     * )
     * @SWG\Tag(name="trainTable")
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

    public function routeOverviewAction(int $trainTableYearId = null, int $routeListId = null): Response
    {
        $routesDisplay = $this->routesDisplayHelper->getRoutesDisplay($trainTableYearId, $routeListId);

        return $this->handleView(
            $this->view([
                'filters' => ['trainTableYears' => $this->doctrine->getRepository(TrainTableYear::class)->findAll()],
                'data' => [
                    'routeLists' => $routesDisplay->routeLists,
                    'selectedRouteList' => $routesDisplay->selectedRouteList,
                    'routes' => $routesDisplay->routes,
                ],
            ], 200)
        );
    }
}
