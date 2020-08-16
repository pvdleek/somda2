<?php

namespace App\Controller\Api;

use App\Entity\Spot;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use App\Model\SpotFilter;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class SpotController extends AbstractFOSRestController
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
     * @param int $maxMonths
     * @param string|null $searchParameters
     * @return Response
     * @SWG\Parameter(
     *     description="The maximum number of months to search for",
     *     enum={1,3,6,12,24,36,48,60,99},
     *     in="path",
     *     name="maxMonths",
     *     type="integer",
     * )
     * @SWG\Parameter(
     *     default="////",
     *     description="A slash separated list of search-parameters, being: \
     *         The location to filter on,\
     *         The dayNumber to filter on (1,2,3,4,5,6,7),\
     *         The spot-date to filter on (d-m-Y),\
     *         The train-number to filter on, use * for wildcard-positions,\
     *         The route-number to filter on, use * for wildcard-positions.\
     *         At least one filter is required, the others can be left blank,\
     *         but 4 slashes need to be present in the path",
     *     in="path",
     *     name="searchParameters",
     *     type="string",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the filtered spots, this can be a potentially slow call, \
     *         especially with little filtering. If a 504 timeout is generated, try extra filters.",
     *     @SWG\Schema(
     *         @SWG\Property(property="filters", type="array", @SWG\Items(ref=@Model(type=SpotFilter::class))),
     *         @SWG\Property(property="data", type="array", @SWG\Items(ref=@Model(type=Spot::class))),
     *     ),
     * )
     * @SWG\Response(
     *     response=404,
     *     description="The request failed",
     *     @SWG\Schema(
     *         @SWG\Property(description="Description of the error", property="error", type="string"),
     *     ),
     * )
     * @SWG\Response(response=504, description="The request timed out, try again with extra filters")
     * @SWG\Tag(name="Spots")
     */
    public function indexAction(int $maxMonths = 1, string $searchParameters = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

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
