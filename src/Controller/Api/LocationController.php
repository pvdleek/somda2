<?php

namespace App\Controller\Api;

use App\Controller\LocationController as WebLocationController;
use App\Entity\Location;
use App\Entity\LocationCategory;
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
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param string $searchMethod
     * @param string $search
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="Returns all locations in the Somda database",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Location::class))
     *     )
     * )
     * @SWG\Tag(name="location")
     */
    public function indexAction(string $searchMethod = null, string $search = null): Response
    {
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
            $categories[$category->getId()] = $category->name;
        }

        return $this->handleView(
            $this->view([
                'filters' => ['category' => $categories],
                'data' => $locations,
            ], 200)
        );
    }
}
