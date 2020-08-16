<?php

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Entity\ForumForum;
use App\Generics\RouteGenerics;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumOverviewHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumForumController
{
    use SortTrait;

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
     * @var ForumOverviewHelper
     */
    private ForumOverviewHelper $forumOverviewHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     * @param RedirectHelper $redirectHelper
     * @param ForumAuthorizationHelper $forumAuthHelper
     * @param ForumOverviewHelper $forumOverviewHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        TemplateHelper $templateHelper,
        RedirectHelper $redirectHelper,
        ForumAuthorizationHelper $forumAuthHelper,
        ForumOverviewHelper $forumOverviewHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
        $this->redirectHelper = $redirectHelper;
        $this->forumAuthHelper = $forumAuthHelper;
        $this->forumOverviewHelper = $forumOverviewHelper;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $categories = $this->forumOverviewHelper->getCategoryArray();

        foreach ($categories as $id => $category) {
            $categories[$id]['forums'] = $this->sortByFieldFilter($category['forums'], 'order');
        }
        $categories = $this->sortByFieldFilter($categories, 'order');

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
            'userIsModerator' => $this->forumAuthHelper->userIsModerator($forum, $this->userHelper->getUser()),
            'discussions' => $discussions,
        ]);
    }
}
