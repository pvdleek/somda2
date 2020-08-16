<?php

namespace App\Controller\Api;

use App\Entity\TrainTableYear;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
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
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
    }

    /**
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all trainTableYears in the Somda database",
     *     @SWG\Schema(
     *         @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=TrainTableYear::class)))
     *     )
     * )
     * @SWG\Tag(name="Train-tables")
     */
    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $trainTableYears = $this->doctrine->getRepository(TrainTableYear::class)->findAll();
        return $this->handleView($this->view($trainTableYears, 200));
    }
}
