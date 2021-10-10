<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Generics\RoleGenerics;
use App\Helpers\ForumSearchHelper;
use App\Helpers\UserHelper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class ForumSearchController extends AbstractFOSRestController
{
    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var ForumSearchHelper
     */
    private ForumSearchHelper $forumSearchHelper;

    /**
     * @param UserHelper $userHelper
     * @param ForumSearchHelper $forumSearchHelper
     */
    public function __construct(UserHelper $userHelper, ForumSearchHelper $forumSearchHelper)
    {
        $this->userHelper = $userHelper;
        $this->forumSearchHelper = $forumSearchHelper;
    }

    /**
     * @param string $searchMethod
     * @param string $terms
     * @return Response
     * @OA\Parameter(
     *     description="Search method to use, 'all' to have all words match, 'some' to let any word match",
     *     in="path",
     *     name="searchMethod",
     *     type="string",
     *     enum={"all","some"}
     * )
     * @OA\Parameter(
     *     description="Words to search for, separated by a space",
     *     in="path",
     *     name="terms",
     *     type="string",
     * )
     * @OA\Response(
     *     response=200,
     *     description="Searches the forum with the given terms and returns an array with results",
     *     @OA\Schema(
     *         @OA\Property(
     *             property="meta",
     *             type="object",
     *             @OA\Property(
     *                 description="The maximum number of results this action returns",
     *                 property="max_number_of_results",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 description="Indicates if there were more results than returned",
     *                 property="more_results",
     *                 type="boolean",
     *             )
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     description="Indicates if the search-term was found in the title",
     *                     property="title_match",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(property="discussion_id", type="integer"),
     *                 @OA\Property(property="discussion_title", type="string"),
     *                 @OA\Property(property="discussion_locked", type="boolean"),
     *                 @OA\Property(property="author_id", type="integer"),
     *                 @OA\Property(property="author_username", type="string"),
     *                 @OA\Property(property="post_id", type="integer"),
     *                 @OA\Property(format="date-time", property="post_timestamp", type="string"),
     *             ),
     *         ),
     *     ),
     * )
     * @OA\Tag(name="Forum")
     */
    public function indexAction(string $searchMethod, string $terms): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        $results = $this->forumSearchHelper->getSearchResults(
            $searchMethod,
            $this->forumSearchHelper->getSearchWords($terms)
        );
        return $this->handleView(
            $this->view([
                'meta' => [
                    'max_number_of_results' => ForumSearchHelper::MAX_RESULTS,
                    'more_results' => count($results) > ForumSearchHelper::MAX_RESULTS
                ],
                'data' => array_slice($results, 0, ForumSearchHelper::MAX_RESULTS),
            ], 200)
        );
    }
}
