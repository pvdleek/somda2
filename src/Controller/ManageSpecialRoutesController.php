<?php

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
        private readonly UserHelper $userHelper,
        private readonly FormHelper $formHelper,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_SPECIAL_ROUTES);

        return $this->templateHelper->render('manageSpecialRoutes/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bijzondere ritten',
            'specialRoutes' => $this->formHelper
                ->getDoctrine()
                ->getRepository(SpecialRoute::class)
                ->findBy([], ['startDate' => 'DESC']),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function editAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_ADMIN_SPECIAL_ROUTES);

        $specialRoute = $this->formHelper->getDoctrine()->getRepository(SpecialRoute::class)->find($id);
        if (\is_null($specialRoute)) {
            $specialRoute = new SpecialRoute();
            $specialRoute->startDate = new \DateTime('+1 day');
            $specialRoute->publicationTimestamp = new \DateTime();
        }
        $form = $this->formHelper->getFactory()->create(SpecialRouteForm::class, $specialRoute);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (\is_null($specialRoute->id)) {
                $this->formHelper->getDoctrine()->getManager()->persist($specialRoute);
                return $this->formHelper->finishFormHandling('Rit toegevoegd', 'manage_special_routes');
            }
            return $this->formHelper->finishFormHandling('Rit bijgewerkt', 'manage_special_routes');
        }

        return $this->templateHelper->render('manageSpecialRoutes/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bijzondere rit',
            'specialRoute' => $specialRoute,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }
}
