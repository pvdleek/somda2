<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use App\Model\SpotFilter;
use App\Repository\SpotRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class SpotController extends AbstractFOSRestController
{
    public function __construct(
        private readonly UserHelper $user_helper,
        private readonly SpotRepository $spot_repository
    ) {
    }

    /**
     * @OA\Parameter(
     *     description="The maximum number of months to search for",
     *     in="path",
     *     name="max_months",
     *     @OA\Schema(type="integer", enum={1,3,6,12,24,36,48,60,99}),
     * )
     * @OA\Parameter(
     *     description="A slash separated list of search-parameters, being: \
     *         The location to filter on,\
     *         The day-number to filter on (1,2,3,4,5,6,7),\
     *         The spot-date to filter on (d-m-Y),\
     *         The train-number to filter on, use * for wildcard-positions,\
     *         The route-number to filter on, use * for wildcard-positions.\
     *         At least one filter is required, the others can be left blank,\
     *         but 4 slashes need to be present in the path",
     *     in="path",
     *     name="searchParameters",
     *     @OA\Schema(type="string", default="////"),
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the filtered spots, this can be a potentially slow call, \
     *         especially with little filtering. If a 504 timeout is generated, try extra filters.",
     *     @OA\Schema(
     *         @OA\Property(property="filters", type="array", @OA\Items(ref=@Model(type=SpotFilter::class))),
     *         @OA\Property(property="data", type="array", @OA\Items(ref=@Model(type=Spot::class))),
     *     ),
     * )
     * @OA\Response(
     *     response=404,
     *     description="The request failed",
     *     @OA\Schema(
     *         @OA\Property(description="Description of the error", property="error", type="string"),
     *     ),
     * )
     * @OA\Response(
     *     response=500,
     *     description="Parsing of the search-parameters failed",
     *     @OA\Schema(
     *         @OA\Property(description="Description of the error", property="error", type="string"),
     *     ),
     * )
     * @OA\Response(response=504, description="The request timed out, try again with extra filters")
     * @OA\Tag(name="Spots")
     */
    public function indexAction(int $max_months = 1, ?string $search_parameters = null): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $spot_filter = new SpotFilter();
        $spots = null;

        if (null !== $search_parameters) {
            $spot_filter->createFromSearchParameters(\explode('/', $search_parameters));

            if (!$spot_filter->isValid()) {
                return $this->handleView($this->view(['error' => 'Getting spots without filters is not allowed'], 404));
            }
            $spots = $this->spot_repository->findWithSpotFilter($max_months, $spot_filter);
        }

        return $this->handleView($this->view([
            'filters' => $spot_filter,
            'data' => $spots
        ], 200));
    }
}
