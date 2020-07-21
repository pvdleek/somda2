<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\RailNews;
use App\Form\RailNews as RailNewsForm;
use App\Form\News as NewsForm;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @IsGranted("ROLE_ADMIN_NEWS")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->templateHelper->render('manageNews/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer nieuws',
            'news' => $this->doctrine->getRepository(News::class)->findBy([], [NewsForm::FIELD_TIMESTAMP => 'DESC']),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN_NEWS")
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
                return $this->formHelper->finishFormHandling('Bericht toegevoegd', RouteGenerics::ROUTE_MANAGE_NEWS);
            }
            return $this->formHelper->finishFormHandling('Bericht bijgewerkt', RouteGenerics::ROUTE_MANAGE_NEWS);
        }

        return $this->templateHelper->render('manageNews/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer nieuwsbericht',
            'news' => $news,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN_RAIL_NEWS")
     * @return Response
     */
    public function railNewsAction(): Response
    {
        return $this->templateHelper->render('manageNews/railNews.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws',
            'railNews' => $this->doctrine
                ->getRepository(RailNews::class)
                ->findBy([], ['approved' => 'ASC', RailNewsForm::FIELD_TIMESTAMP => 'DESC'], 100),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN_RAIL_NEWS")
     * @param Request $request
     * @return JsonResponse
     */
    public function railNewsDisapproveAction(Request $request): JsonResponse
    {
        $ids = array_filter(explode(',', array_keys($request->request->all())[0]));
        foreach ($ids as $id) {
            $railNews = $this->doctrine->getRepository(RailNews::class)->find($id);
            $railNews->approved = true;
        }

        return new JsonResponse();
    }

    /**
     * @IsGranted("ROLE_ADMIN_RAIL_NEWS")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function railNewsEditAction(Request $request, int $id)
    {
        $railNews = $this->doctrine->getRepository(RailNews::class)->find($id);
        if (is_null($railNews)) {
            throw new AccessDeniedException();
        }
        $form = $this->formHelper->getFactory()->create(RailNewsForm::class, $railNews);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $railNews->approved = true;
            return $this->formHelper->finishFormHandling('Bericht bijgewerkt', RouteGenerics::ROUTE_MANAGE_RAIL_NEWS);
        }

        return $this->templateHelper->render('manageNews/railNewsItem.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws bericht',
            'railNews' => $railNews,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
