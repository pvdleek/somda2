<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\News;
use App\Entity\RailNews;
use App\Form\RailNews as RailNewsForm;
use App\Form\News as NewsForm;
use App\Generics\RoleGenerics;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ManageNewsController
{
    public function __construct(
        private readonly UserHelper $userHelper,
        private readonly FormHelper $formHelper,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_NEWS);

        return $this->templateHelper->render('manageNews/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer nieuws',
            'news' => $this->formHelper
                ->getDoctrine()
                ->getRepository(News::class)
                ->findBy([], [NewsForm::FIELD_TIMESTAMP => 'DESC']),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function editAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_NEWS);

        $news = $this->formHelper->getDoctrine()->getRepository(News::class)->find($id);
        if (null === $news) {
            $news = new News();
            $news->timestamp = new \DateTime();
        }
        $form = $this->formHelper->getFactory()->create(NewsForm::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $news->id) {
                $this->formHelper->getDoctrine()->getManager()->persist($news);
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

    public function railNewsAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_RAIL_NEWS);

        return $this->templateHelper->render('manageNews/railNews.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws',
            'railNews' => $this->formHelper->getDoctrine()
                ->getRepository(RailNews::class)
                ->findForManagement(100)
        ]);
    }

    public function railNewsDisapproveAction(Request $request): JsonResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_RAIL_NEWS);

        $ids = \array_filter(\explode(',', \array_keys($request->request->all())[0]));
        foreach ($ids as $id) {
            $railNews = $this->formHelper->getDoctrine()->getRepository(RailNews::class)->find($id);
            $railNews->approved = true;
        }

        return new JsonResponse();
    }

    public function railNewsEditAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_RAIL_NEWS);

        $railNews = $this->formHelper->getDoctrine()->getRepository(RailNews::class)->find($id);
        if (null === $railNews) {
            throw new AccessDeniedException('This rail-news item does not exist');
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
