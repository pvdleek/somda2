<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserBan;
use App\Generics\RoleGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManageUsersController
{
    public function __construct(
        private readonly UserHelper $userHelper,
        private readonly FormHelper $formHelper,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function bansAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_BANS);

        return $this->templateHelper->render('manageUsers/bans.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bans',
            'users' => $this->formHelper->getDoctrine()->getRepository(User::class)->findBanned(),
        ]);
    }

    public function banAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_BANS);

        $user = $this->formHelper->getDoctrine()->getRepository(User::class)->find($id);
        $form = $this->formHelper->getFactory()->create(UserBan::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formHelper->finishFormHandling('Ban bijgewerkt', 'manage_bans');
        }

        return $this->templateHelper->render('manageUsers/ban.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer ban',
            'user' => $user,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
