<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserBan;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManageUsersController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(ManagerRegistry $doctrine, FormHelper $formHelper, TemplateHelper $templateHelper)
    {
        $this->doctrine = $doctrine;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @return Response
     */
    public function bansAction(): Response
    {
        return $this->templateHelper->render('manageUsers/bans.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bans',
            'users' => $this->doctrine->getRepository(User::class)->findBanned(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function banAction(Request $request, int $id)
    {
        $user = $this->doctrine->getRepository(User::class)->find($id);
        $form = $this->formHelper->getFactory()->create(UserBan::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formHelper->finishFormHandling('Ban bijgewerkt', 'manage_bans');
        }

        return $this->templateHelper->render('manageUsers/ban.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer ban',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
