<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumForum;
use App\Generics\RouteGenerics;
use App\Helpers\ForumAuthorizationHelper;
use App\Helpers\ForumOverviewHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
use App\Traits\SortTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumForumController
{
    use SortTrait;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly ForumAuthorizationHelper $forum_authorization_helper,
        private readonly ForumOverviewHelper $forum_overview_helper,
        private readonly RedirectHelper $redirect_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
    ) {
    }

    public function indexAction(): Response
    {
        $categories = $this->forum_overview_helper->getCategoryArray();

        foreach ($categories as $id => $category) {
            $categories[$id]['forums'] = $this->sortByFieldFilter($category['forums'], 'order');
        }
        $categories = $this->sortByFieldFilter($categories, 'order');

        return $this->template_helper->render('forum/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - Overzicht',
            'categories' => $categories
        ]);
    }

    public function forumAction(int $id): Response|RedirectResponse
    {
        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (null === $forum) {
            return $this->redirect_helper->redirectToRoute(RouteGenerics::ROUTE_FORUM);
        }

        return $this->template_helper->render('forum/forum.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Forum - '.$forum->name,
            TemplateHelper::PARAMETER_FORUM => $forum,
            'user_is_moderator' => $this->forum_authorization_helper->userIsModerator($forum, $this->user_helper->getUser()),
            'discussions' => $this->forum_discussion_repository->findByForum($forum, $this->user_helper->getUser()),
        ]);
    }
}
