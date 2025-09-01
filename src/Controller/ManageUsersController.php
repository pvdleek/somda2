<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserBan;
use App\Generics\RoleGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManageUsersController
{
    public function __construct(
        private readonly UserHelper $user_helper,
        private readonly FormHelper $form_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserRepository $user_repository,
    ) {
    }

    public function bansAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_BANS);

        return $this->template_helper->render('manageUsers/bans.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bans',
            'users' => $this->user_repository->findBanned(),
        ]);
    }

    public function banAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_BANS);

        $user = $this->user_repository->find($id);
        $form = $this->form_helper->getFactory()->create(UserBan::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->form_helper->finishFormHandling('Ban bijgewerkt', 'manage_bans');
        }

        return $this->template_helper->render('manageUsers/ban.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer ban',
            'user' => $user,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
