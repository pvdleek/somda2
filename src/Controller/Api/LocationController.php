<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\LocationController as WebLocationController;
use App\Entity\Location;
use App\Entity\LocationCategory;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends AbstractFOSRestController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
    ) {
    }

    /**
     * @OA\Parameter(
     *     description="The search-method",
     *     in="path",
     *     name="searchMethod",
     *     @OA\Schema(type="string", enum={"letter","specifiek","naam","omschrijving"}),
     * )
     * @OA\Parameter(
     *     description="The search-string",
     *     in="path",
     *     name="search",
     *     @OA\Schema(type="string"),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all locations in the Somda database",
     *     @OA\Property(
     *         property="filters",
     *         type="object",
     *         @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *     ),
     *     @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=Location::class))),
     * )
     * @OA\Tag(name="Locations")
     */
    public function indexAction(?string $searchMethod = null, ?string $search = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        switch ($searchMethod) {
            case WebLocationController::SEARCH_METHOD_CHARACTER:
                $locations = $this->doctrine->getRepository(Location::class)->findByName($search . '%');
                break;
            case WebLocationController::SEARCH_METHOD_SINGLE:
                $locations = $this->doctrine->getRepository(Location::class)->findByName($search);
                break;
            case WebLocationController::SEARCH_METHOD_NAME:
                $locations = $this->doctrine->getRepository(Location::class)->findByName('%' . $search . '%');
                break;
            case WebLocationController::SEARCH_METHOD_DESCRIPTION:
                $locations = $this->doctrine->getRepository(Location::class)->findByDescription('%' . $search . '%');
                break;
            default:
                $locations = $this->doctrine->getRepository(Location::class)->findAll();
                break;
        }

        $categories = [];
        $locationCategories = $this->doctrine->getRepository(LocationCategory::class)->findAll();
        foreach ($locationCategories as $category) {
            $categories[] = $category->name;
        }

        return $this->handleView(
            $this->view([
                'filters' => ['category' => $categories],
                'data' => $locations,
            ], 200)
        );
    }
}
