<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\News;
use App\Form\RailNews as RailNewsForm;
use App\Form\News as NewsForm;
use App\Generics\RoleGenerics;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Repository\NewsRepository;
use App\Repository\RailNewsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ManageNewsController
{
    public function __construct(
        private readonly UserHelper $user_helper,
        private readonly FormHelper $form_helper,
        private readonly TemplateHelper $template_helper,
        private readonly NewsRepository $news_repository,
        private readonly RailNewsRepository $rail_news_repository,
    ) {
    }

    public function indexAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_NEWS);

        return $this->template_helper->render('manageNews/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer nieuws',
            'news' => $this->news_repository->findBy([], [NewsForm::FIELD_TIMESTAMP => 'DESC']),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function editAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_NEWS);

        $news = $this->news_repository->find($id);
        if (null === $news) {
            $news = new News();
            $news->timestamp = new \DateTime();
        }
        $form = $this->form_helper->getFactory()->create(NewsForm::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $news->id) {
                $this->form_helper->getDoctrine()->getManager()->persist($news);
                return $this->form_helper->finishFormHandling('Bericht toegevoegd', RouteGenerics::ROUTE_MANAGE_NEWS);
            }
            return $this->form_helper->finishFormHandling('Bericht bijgewerkt', RouteGenerics::ROUTE_MANAGE_NEWS);
        }

        return $this->template_helper->render('manageNews/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer nieuwsbericht',
            'news' => $news,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function railNewsAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_RAIL_NEWS);

        return $this->template_helper->render('manageNews/railNews.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws',
            'railNews' => $this->rail_news_repository->findForManagement(100)
        ]);
    }

    public function railNewsDisapproveAction(Request $request): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_RAIL_NEWS);

        $ids = \array_filter(\explode(',', \array_keys($request->request->all())[0]));
        foreach ($ids as $id) {
            $rail_news = $this->rail_news_repository->find($id);
            $rail_news->approved = true;
        }

        return new JsonResponse();
    }

    public function railNewsEditAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_RAIL_NEWS);

        $rail_news = $this->rail_news_repository->find($id);
        if (null === $rail_news) {
            throw new AccessDeniedException('This rail-news item does not exist');
        }
        $form = $this->form_helper->getFactory()->create(RailNewsForm::class, $rail_news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rail_news->approved = true;
            return $this->form_helper->finishFormHandling('Bericht bijgewerkt', RouteGenerics::ROUTE_MANAGE_RAIL_NEWS);
        }

        return $this->template_helper->render('manageNews/railNewsItem.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer spoornieuws bericht',
            'railNews' => $rail_news,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
