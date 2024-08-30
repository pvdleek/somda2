<?php

namespace App\Controller\Api;

use App\Entity\ForumDiscussion;
use App\Entity\RailNews;
use App\Form\RailNews as RailNewsForm;
use App\Generics\RoleGenerics;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractFOSRestController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
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
     *                 property="discussionId",
     *                 type="integer",
     *             ),
     *             @OA\Property(
     *                 description="The title of the discussion",
     *                 property="discussionTitle",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                description="Whether the discussion is locked",
     *                property="discussionLocked",
     *                type="boolean",
     *             ),
     *             @OA\Property(
     *                 description="The timestamp of the last post in the discussion (Y-m-d H:i:s)",
     *                 property="lastPostTimestamp",
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
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_API_USER);

        // Get the last forum topic where a response was posted
        $discussion = $this->doctrine->getRepository(ForumDiscussion::class)->findLastDiscussion();
        $lastForumPost = null === $discussion ? null : [
            'discussionId' => $discussion['id'],
            'discussionTitle' => $discussion['title'],
            'discussionLocked' => $discussion['locked'],
            'lastPostTimestamp' => $discussion['max_post_timestamp'],
        ];

        // Get one of the last rail-news items
        /**
         * @var RailNews $railNewsItem
         */
        $railNewsItem = $this->doctrine->getRepository(RailNews::class)->findBy(
            ['active' => true, 'approved' => true],
            [RailNewsForm::FIELD_TIMESTAMP => 'DESC'],
            50
        )[\random_int(0, 4)];

        return $this->handleView($this->view([
            'lastForumPost' => $lastForumPost,
            'railNews' => [
                'title' => $railNewsItem->title,
                'timestamp' => $railNewsItem->timestamp->format('Y-m-d H:i:s'),
                'url' => $railNewsItem->url,
            ]
        ], 200));
    }
}
