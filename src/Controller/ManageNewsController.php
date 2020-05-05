<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\RailNews;
use App\Form\RailNews as RailNewsForm;
use App\Form\News as NewsForm;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ManageNewsController
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
        return $this->templateHelper->render('manageNews/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer nieuws',
            'news' => $this->doctrine->getRepository(News::class)->findBy([], ['timestamp' => 'DESC']),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editAction(Request $request, int $id)
    {
        $news = $this->doctrine->getRepository(News::class)->find($id);
        if (is_null($news)) {
            $news = new News();
            $news->timestamp = new DateTime();
        }
        $form = $this->formHelper->getFactory()->create(NewsForm::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($news->getId())) {
                $this->doctrine->getManager()->persist($news);
                return $this->formHelper->finishFormHandling('Bericht toegevoegd', 'manage_rail_news');
            }
            return $this->formHelper->finishFormHandling('Bericht bijgewerkt', 'manage_rail_news');
        }

        return $this->templateHelper->render('manageNews/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer nieuwsbericht',
            'news' => $news,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @return Response
     */
    public function railNewsAction(): Response
    {
        return $this->templateHelper->render('manageNews/railNews.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws',
            'railNews' => $this->doctrine
                ->getRepository(RailNews::class)
                ->findBy([], ['approved' => 'ASC', 'timestamp' => 'DESC'], 100),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function railNewsEditAction(Request $request, int $id)
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

        return $this->templateHelper->render('manageNews/railNewsItem.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws bericht',
            'railNews' => $railNews,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
