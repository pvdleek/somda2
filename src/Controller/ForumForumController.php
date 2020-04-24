<?php

namespace App\Controller;

use App\Entity\ForumCategory;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumForumController
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var UserHelper
     */
    private $userHelper;

    /**
     * @var TemplateHelper
     */
    private $templateHelper;

    /**
     * @var RedirectHelper
     */
    private $redirectHelper;

    /**
     * @var ForumAuthorizationHelper
     */
    private $forumAuthHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     * @param RedirectHelper $redirectHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        TemplateHelper $templateHelper,
        RedirectHelper $redirectHelper,
        ForumAuthorizationHelper $forumAuthHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
        $this->redirectHelper = $redirectHelper;
        $this->forumAuthHelper = $forumAuthHelper;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        /**
         * @var ForumCategory[] $forumCategories
         */
        $forumCategories = $this->doctrine->getRepository(ForumCategory::class)->findBy([], ['order' => 'ASC']);
        $categories = [];
        foreach ($forumCategories as $category) {
            $categories[] = [
                'category' => $category,
                'forums' => $this->doctrine->getRepository(ForumForum::class)->findByCategory($category),
            ];
        }

        return $this->templateHelper->render('forum/index.html.twig', [
            'pageTitle' => 'Forum - Overzicht',
            'categories' => $categories
        ]);
    }

    /**
     * @param int $id
     * @return Response|RedirectResponse
     */
    public function forumAction(int $id): Response
    {
        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (is_null($forum)) {
            return $this->redirectHelper->redirectToRoute('forum');
        }

        $discussions = $this->doctrine
            ->getRepository(ForumDiscussion::class)
            ->findByForum($forum, $this->userHelper->getUser());
        return $this->templateHelper->render('forum/forum.html.twig', [
            'pageTitle' => 'Forum - ' . $forum->name,
            'forum' => $forum,
            'userIsModerator' => $this->forumAuthHelper->userIsModerator(
                $forum->getDiscussions()[0],
                $this->userHelper->getUser()
            ),
            'discussions' => $discussions,
        ]);
    }
}
