<?php

namespace App\Controller;

use App\Helpers\Controller\TrainTableHelper;
use App\Helpers\RoutesDisplayHelper;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractFOSRestController
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
     * @var RoutesDisplayHelper
     */
    private RoutesDisplayHelper $routesDisplayHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TrainTableHelper $trainTableHelper
     * @param RoutesDisplayHelper $routesDisplayHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        TrainTableHelper $trainTableHelper,
        RoutesDisplayHelper $routesDisplayHelper
    ) {
        $this->doctrine = $doctrine;
        $this->trainTableHelper = $trainTableHelper;
        $this->routesDisplayHelper = $routesDisplayHelper;
    }

    /**
     * @param int|null $trainTableYearId
     * @param string|null $routeNumber
     * @return Response
     */
    public function trainTableAction(int $trainTableYearId = null, string $routeNumber = null): Response
    {
        $trainTableLines = [];

        if (!is_null($trainTableYearId)) {
            $this->trainTableHelper->setTrainTableYear($trainTableYearId);
            $this->trainTableHelper->setRoute($routeNumber);
            $trainTableLines = $this->trainTableHelper->getTrainTableLines();
        }

        return $this->handleView($this->view($trainTableLines, 200));
    }
}
