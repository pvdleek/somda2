<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\SpecialRoute;
use App\Form\SpecialRoute as SpecialRouteForm;
use App\Generics\RoleGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManageSpecialRoutesController
{
    public function __construct(
        private readonly UserHelper $user_helper,
        private readonly FormHelper $form_helper,
        private readonly TemplateHelper $template_helper,
    ) {
    }

    public function indexAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_SPECIAL_ROUTES);

        return $this->template_helper->render('manageSpecialRoutes/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bijzondere ritten',
            'specialRoutes' => $this->form_helper
                ->getDoctrine()
                ->getRepository(SpecialRoute::class)
                ->findBy([], ['start_date' => 'DESC']),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function editAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_SPECIAL_ROUTES);

        $special_route = $this->form_helper->getDoctrine()->getRepository(SpecialRoute::class)->find($id);
        if (null === $special_route) {
            $special_route = new SpecialRoute();
            $special_route->start_date = new \DateTime('+1 day');
            $special_route->publication_timestamp = new \DateTime();
        }
        $form = $this->form_helper->getFactory()->create(SpecialRouteForm::class, $special_route);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $special_route->id) {
                $this->form_helper->getDoctrine()->getManager()->persist($special_route);
                return $this->form_helper->finishFormHandling('Rit toegevoegd', 'manage_special_routes');
            }
            return $this->form_helper->finishFormHandling('Rit bijgewerkt', 'manage_special_routes');
        }

        return $this->template_helper->render('manageSpecialRoutes/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bijzondere rit',
            'specialRoute' => $special_route,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
