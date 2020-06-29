<?php

namespace App\Controller\Api;

use App\Entity\TrainTableYear;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class TrainTableYearController extends AbstractFOSRestController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all trainTableYears in the Somda database",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=TrainTableYear::class))
     *     )
     * )
     * @SWG\Tag(name="trainTable")
     */
    public function indexAction(): Response
    {
        $trainTableYears = $this->doctrine->getRepository(TrainTableYear::class)->findAll();
        return $this->handleView($this->view($trainTableYears, 200));
    }
}
