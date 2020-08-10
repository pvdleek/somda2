<?php

namespace App\Controller\Api;

use App\Entity\Spot;
use App\Model\SpotFilter;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class SpotController extends AbstractFOSRestController
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
     * @IsGranted("ROLE_API_USER")
     * @param int $maxMonths
     * @param string|null $searchParameters
     * @return Response
     * @SWG\Parameter(
     *     description="The maximum number of months to search for",
     *     enum="{1,3,6,12,24,36,48,60,99}"
     *     in="path",
     *     name="maxMonths",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     description="The location to filter on",
     *     in="path",
     *     name="location",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     description="The dayNumber to filter on",
     *     enum={1,2,3,4,5,6,7}
     *     in="path",
     *     name="dayNumber",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     description="The spot-date to filter on (d-m-Y)",
     *     in="path",
     *     name="spotDate",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     description="The train-number to filter on, use * for wildcard-positions",
     *     in="path",
     *     name="trainNumber",
     *     type="string",
     * )
     * @SWG\Parameter(
     *     description="The route-number to filter on, use * for wildcard-positions",
     *     in="path",
     *     name="routeNumber",
     *     type="string",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the filtered spots",
     *     @SWG\Schema(
     *         @SWG\Property(property="filters", type="array", @SWG\Items(ref=@Model(type=SpotFilter::class))),
     *         @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=Spot::class))),
     *     ),
     * )
     * @SWG\Tag(name="Spots")
     */
    public function indexAction(int $maxMonths = 1, string $searchParameters = null): Response
    {
        $spotFilter = new SpotFilter();
        $spots = null;

        if (!is_null($searchParameters)) {
            $spotFilter->createFromSearchParameters(explode('/', $searchParameters));

            if (!$spotFilter->isValid()) {
                return $this->handleView($this->view(['error' => 'Getting spots without filters is not allowed'], 404));
            }
            $spots = $this->doctrine->getRepository(Spot::class)->findWithSpotFilter($maxMonths, $spotFilter);
        }

        return $this->handleView($this->view([
            'filters' => $spotFilter,
            'data' => $spots
        ], 200));
    }
}
