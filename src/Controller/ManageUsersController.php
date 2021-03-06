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
    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param UserHelper $userHelper
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(UserHelper $userHelper, FormHelper $formHelper, TemplateHelper $templateHelper)
    {
        $this->userHelper = $userHelper;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @return Response
     */
    public function bansAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_BANS);

        return $this->templateHelper->render('manageUsers/bans.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bans',
            'users' => $this->formHelper->getDoctrine()->getRepository(User::class)->findBanned(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function banAction(Request $request, int $id)
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
