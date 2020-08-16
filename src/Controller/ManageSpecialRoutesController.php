<?php

namespace App\Controller;

use App\Entity\SpecialRoute;
use App\Form\SpecialRoute as SpecialRouteForm;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManageSpecialRoutesController
{
    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(FormHelper $formHelper, TemplateHelper $templateHelper)
    {
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @IsGranted("ROLE_ADMIN_SPECIAL_ROUTES")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->templateHelper->render('manageSpecialRoutes/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer bijzondere ritten',
            'specialRoutes' => $this->formHelper
                ->getDoctrine()
                ->getRepository(SpecialRoute::class)
                ->findBy([], ['startDate' => 'DESC']),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN_SPECIAL_ROUTES")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editAction(Request $request, int $id)
    {
        $specialRoute = $this->formHelper->getDoctrine()->getRepository(SpecialRoute::class)->find($id);
        if (is_null($specialRoute)) {
            $specialRoute = new SpecialRoute();
            $specialRoute->startDate = new DateTime('+1 day');
            $specialRoute->publicationTimestamp = new DateTime();
        }
        $form = $this->formHelper->getFactory()->create(SpecialRouteForm::class, $specialRoute);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($specialRoute->getId())) {
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
