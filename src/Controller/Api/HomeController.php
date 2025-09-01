<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\RailNews;
use App\Form\RailNews as RailNewsForm;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractFOSRestController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $user_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
    ) {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns information for the home-screen of the app",
     *     @OA\Schema(
     *         @OA\Property(
     *             property="lastForumPost",
     *             type="object",
     *             @OA\Property(
     *                 description="The id of the discussion",
     *                 property="discussion_id",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 description="The title of the discussion",
     *                 property="discussion_title",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                description="Whether the discussion is locked",
     *                property="discussion_locked",
     *                type="boolean",
     *             ),
     *             @OA\Property(
     *                 description="The timestamp of the last post in the discussion (Y-m-d H:i:s)",
     *                 property="last_post_timestamp",
     *                 type="string",
     *             ),
     *         ),
     *         @OA\Property(
     *             property="railNews",
     *             type="object",
     *             @OA\Property(
     *                 description="The title of the rail-news item",
     *                 property="title",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 description="The timestamp of the rail-news item (Y-m-d H:i:s)",
     *                 property="timestamp",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 description="The external link for the rail-news item",
     *                 property="url",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     * )
     * @OA\Tag(name="Home")
     * @throws \Exception
     */
    public function indexAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        // Get the last forum topic where a response was posted
        $discussion = $this->forum_discussion_repository->findLastDiscussion();
        $last_forum_post = null === $discussion ? null : [
            'discussion_id' => $discussion['id'],
            'discussion_title' => $discussion['title'],
            'discussion_locked' => $discussion['locked'],
            'last_post_timestamp' => $discussion['max_post_timestamp'],
        ];

        // Get one of the last rail-news items
        /**
         * @var RailNews $rail_news_item
         */
        $rail_news_item = $this->doctrine->getRepository(RailNews::class)->findBy(
            ['active' => true, 'approved' => true],
            [RailNewsForm::FIELD_TIMESTAMP => 'DESC'],
            50
        )[\random_int(0, 4)];

        return $this->handleView($this->view([
            'lastForumPost' => $last_forum_post,
            'railNews' => [
                'title' => $rail_news_item->title,
                'timestamp' => $rail_news_item->timestamp->format('Y-m-d H:i:s'),
                'url' => $rail_news_item->url,
            ]
        ], 200));
    }
}
