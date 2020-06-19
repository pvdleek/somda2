<?php

namespace App\Controller;

use App\Entity\ForumCategory;
use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Generics\RouteGenerics;
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
    private ManagerRegistry $doctrine;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @var ForumAuthorizationHelper
     */
    private ForumAuthorizationHelper $forumAuthHelper;

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
            $categoryItem = ['category' => $category, 'forums' => []];
            $forums = $this->doctrine
                ->getRepository(ForumForum::class)
                ->findByCategory($category, $this->userHelper->getUser());
            foreach ($forums as $forum) {
                $forumEntity = $this->doctrine->getRepository(ForumForum::class)->find($forum['id']);
                if ($this->forumAuthHelper->mayView($forumEntity, $this->userHelper->getUser())) {
                    $categoryItem['forums'][] = $forum;
                }
            }
            $categories[] = $categoryItem;
        }

        return $this->templateHelper->render('forum/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - Overzicht',
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
            return $this->redirectHelper->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        $discussions = $this->doctrine
            ->getRepository(ForumDiscussion::class)
            ->findByForum($forum, $this->userHelper->getUser());
        return $this->templateHelper->render('forum/forum.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - ' . $forum->name,
            TemplateHelper::PARAMETER_FORUM => $forum,
            'userIsModerator' => $this->forumAuthHelper->userIsModerator(
                $forum->getDiscussions()[0],
                $this->userHelper->getUser()
            ),
            'discussions' => $discussions,
        ]);
    }
}
