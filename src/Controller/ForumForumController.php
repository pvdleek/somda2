<?php
declare(strict_types=1);

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

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly RedirectHelper $redirectHelper,
        private readonly ForumAuthorizationHelper $forumAuthHelper,
        private readonly ForumOverviewHelper $forumOverviewHelper,
    ) {
    }

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

    public function forumAction(int $id): Response|RedirectResponse
    {
        /**
         * @var ForumForum $forum
         */
        $forum = $this->doctrine->getRepository(ForumForum::class)->find($id);
        if (null === $forum) {
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
