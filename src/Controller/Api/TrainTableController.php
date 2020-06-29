<?php

namespace App\Controller\Api;

use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Helpers\Controller\TrainTableHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
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
     * @param ManagerRegistry $doctrine
     * @param TrainTableHelper $trainTableHelper
     */
    public function __construct(ManagerRegistry $doctrine, TrainTableHelper $trainTableHelper)
    {
        $this->doctrine = $doctrine;
        $this->trainTableHelper = $trainTableHelper;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     * @SWG\Parameter(
     *     default="The current trainTableYear",
     *     description="The unique identifier of the trainTableYear",
     *     in="query",
     *     name="trainTableYearId",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     description="The routeNumber",
     *     in="query",
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
    public function indexAction(Request $request): Response
    {
        $trainTableLines = [];
        $routeNumber = $request->get('routeNumber');
        $trainTableYearId = $request->get('trainTableYearId');

        if (!is_null($routeNumber)) {
            if (!is_numeric($trainTableYearId)) {
                $trainTableYearId = $this->doctrine
                    ->getRepository(TrainTableYear::class)
                    ->findTrainTableYearByDate(new DateTime())
                    ->getId();
            }
            $this->trainTableHelper->setTrainTableYear($trainTableYearId);
            $this->trainTableHelper->setRoute($routeNumber);
            $trainTableLines = $this->trainTableHelper->getTrainTableLines();
        }

        return $this->handleView($this->view($trainTableLines, 200));
    }
}
