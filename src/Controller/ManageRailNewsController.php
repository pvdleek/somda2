<?php

namespace App\Controller;

use App\Entity\RailNews;
use App\Entity\User;
use App\Form\RailNews as RailNewsForm;
use App\Form\UserBan;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ManageRailNewsController
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
    public function indexAction(): Response
    {
        return $this->templateHelper->render('manageRailNews/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws',
            'railNews' => $this->doctrine->getRepository(RailNews::class)->findBy([], ['approved' => 'ASC', 'timestamp' => 'DESC'], 100),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $id)
    {
        $railNews = $this->doctrine->getRepository(RailNews::class)->find($id);
        if (is_null($railNews)) {
            throw new AccessDeniedHttpException();
        }
        $form = $this->formHelper->getFactory()->create(RailNewsForm::class, $railNews);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formHelper->finishFormHandling('Bericht bijgewerkt', 'manage_rail_news');
        }

        return $this->templateHelper->render('manageRailNews/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws bericht',
            'railNews' => $railNews,
            'form' => $form->createView(),
        ]);
    }
}
