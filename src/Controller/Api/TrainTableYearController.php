<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\TrainTableYear;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class TrainTableYearController extends AbstractFOSRestController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
    ) {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns all trainTableYears in the Somda database",
     *     @OA\Schema(
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=TrainTableYear::class)))
     *     )
     * )
     * @OA\Tag(name="Train-tables")
     */
    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $trainTableYears = $this->doctrine->getRepository(TrainTableYear::class)->findAll();
        return $this->handleView($this->view($trainTableYears, 200));
    }
}
