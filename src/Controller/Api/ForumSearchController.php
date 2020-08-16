<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Generics\RoleGenerics;
use App\Helpers\ForumSearchHelper;
use App\Helpers\UserHelper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Swagger\Annotations as SWG;
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
     * @SWG\Parameter(
     *     description="Search method to use, 'all' to have all words match, 'some' to let any word match",
     *     in="path",
     *     name="searchMethod",
     *     type="string",
     *     enum={"all","some"}
     * )
     * @SWG\Parameter(
     *     description="Words to search for, separated by a space",
     *     in="path",
     *     name="terms",
     *     type="string",
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Searches the forum with the given terms and returns an array with results",
     *     @SWG\Schema(
     *         @SWG\Property(
     *             property="meta",
     *             type="object",
     *             @SWG\Property(
     *                 description="The maximum number of results this action returns",
     *                 property="max_number_of_results",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 description="Indicates if there were more results than returned",
     *                 property="more_results",
     *                 type="boolean",
     *             )
     *         ),
     *         @SWG\Property(
     *             property="data",
     *             type="array",
     *             @SWG\Items(
     *                 type="object",
     *                 @SWG\Property(
     *                     description="Indicates if the search-term was found in the title",
     *                     property="title_match",
     *                     type="boolean"
     *                 ),
     *                 @SWG\Property(property="discussion_id", type="integer"),
     *                 @SWG\Property(property="discussion_title", type="string"),
     *                 @SWG\Property(property="discussion_locked", type="boolean"),
     *                 @SWG\Property(property="author_id", type="integer"),
     *                 @SWG\Property(property="author_username", type="string"),
     *                 @SWG\Property(property="post_id", type="integer"),
     *                 @SWG\Property(format="date-time", property="post_timestamp", type="string"),
     *             ),
     *         ),
     *     ),
     * )
     * @SWG\Tag(name="Forum")
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
