<?php

namespace App\Controller\Api;

use App\Helpers\ForumSearchHelper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class ForumSearchController extends AbstractFOSRestController
{
    /**
     * @var ForumSearchHelper
     */
    private ForumSearchHelper $forumSearchHelper;

    /**
     * @param ForumSearchHelper $forumSearchHelper
     */
    public function __construct(ForumSearchHelper $forumSearchHelper)
    {
        $this->forumSearchHelper = $forumSearchHelper;
    }

    /**
     * @param string $method
     * @param string $terms
     * @return Response
     */
    public function indexAction(string $method, string $terms): Response
    {
        $results = $this->forumSearchHelper->getSearchResults(
            $method,
            $this->forumSearchHelper->getSearchWords($terms)
        );
        return $this->handleView(
            $this->view([
                'meta' => ['more_results' => count($results) > ForumSearchHelper::MAX_RESULTS],
                'data' => array_slice($results, 0, ForumSearchHelper::MAX_RESULTS),
            ], 200)
        );
    }
}
