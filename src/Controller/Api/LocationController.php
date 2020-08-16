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
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends AbstractFOSRestController
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
     * @param UserHelper $userHelper
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine, UserHelper $userHelper)
    {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
    }

    /**
     * @param string|null $searchMethod
     * @param string|null $search
     * @return Response
     * @SWG\Parameter(
     *     description="The search-method",
     *     enum={"letter","specifiek","naam","omschrijving"},
     *     in="path",
     *     name="searchMethod",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     description="The search-string",
     *     in="path",
     *     name="search",
     *     type="string",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns all locations in the Somda database",
     *     @SWG\Property(
     *         property="filters",
     *         type="object",
     *         @SWG\Property(property="categories", type="array", @SWG\Items(type="string")),
     *     ),
     *     @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=Location::class))),
     * )
     * @SWG\Tag(name="Locations")
     */
    public function indexAction(string $searchMethod = null, string $search = null): Response
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
