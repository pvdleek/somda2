<?php

declare(strict_types=1);

namespace App\Controller;

use App\Generics\RoleGenerics;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\ForumDiscussionRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumUnreadController
{
    public function __construct(
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
        private readonly RedirectHelper $redirect_helper,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
    ) {
    }

    public function indexAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussions = $this->forum_discussion_repository->findUnread($this->user_helper->getUser());

        return $this->template_helper->render('forum/unread.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Ongelezen zaken',
            'discussions' => $discussions,
        ]);
    }

    public function markReadAction(): RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $this->forum_discussion_repository->markAllPostsAsRead($this->user_helper->getUser());

        return $this->redirect_helper->redirectToRoute('forum_unread');
    }
}
