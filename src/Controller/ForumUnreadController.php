<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForumDiscussion;
use App\Generics\RoleGenerics;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ForumUnreadController
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
     * @param ManagerRegistry $doctrine
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     * @param RedirectHelper $redirectHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        UserHelper $userHelper,
        TemplateHelper $templateHelper,
        RedirectHelper $redirectHelper
    ) {
        $this->doctrine = $doctrine;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
        $this->redirectHelper = $redirectHelper;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $discussions = $this->doctrine->getRepository(ForumDiscussion::class)->findUnread($this->userHelper->getUser());

        return $this->templateHelper->render('forum/unread.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Ongelezen zaken',
            'discussions' => $discussions,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function markReadAction(): RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $this->doctrine->getRepository(ForumDiscussion::class)->markAllPostsAsRead($this->userHelper->getUser());

        return $this->redirectHelper->redirectToRoute('unread_stuff');
    }
}
