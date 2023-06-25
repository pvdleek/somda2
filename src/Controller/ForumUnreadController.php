<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Generics\RoleGenerics;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumUnreadController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly RedirectHelper $redirectHelper,
    ) {
    }

    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussions = $this->doctrine->getRepository(ForumDiscussion::class)->findUnread($this->userHelper->getUser());

        return $this->templateHelper->render('forum/unread.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Ongelezen zaken',
            'discussions' => $discussions,
        ]);
    }

    public function markReadAction(): RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $this->doctrine->getRepository(ForumDiscussion::class)->markAllPostsAsRead($this->userHelper->getUser());

        return $this->redirectHelper->redirectToRoute('forum_unread');
    }
}
