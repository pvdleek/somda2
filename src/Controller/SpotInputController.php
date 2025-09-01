<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Spot;
use App\Form\SpotBulk;
use App\Generics\RoleGenerics;
use App\Helpers\FormHelper;
use App\Helpers\SpotInputHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SpotInputController
{
    public function __construct(
        private readonly FormHelper $formHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly UserHelper $userHelper,
        private readonly SpotInputHelper $spotInputHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(Request $request): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_NEW);

        $form = $this->formHelper->getFactory()->create(
            SpotBulk::class,
            null,
            ['defaultLocation' => $this->userHelper->getDefaultLocation()]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $spotLines = \array_filter(\preg_split('/$\R?^/m', $form->get('spots')->getData()));
            $spotIdArray = $this->spotInputHelper->processSpotLines(
                $spotLines,
                $this->userHelper->getUser(),
                $form->get('date')->getData(),
                $form->get('location')->getData()
            );

            if (\count($spotIdArray) > 0) {
                return $this->formHelper->finishFormHandling(
                    'Spot(s) opgeslagen',
                    'spot_input_feedback',
                    ['id_list' => \implode('/', $spotIdArray)]
                );
            }

            return $this->formHelper->finishFormHandling('Er konden geen spots worden opgeslagen', 'spot_input');
        }

        return $this->templateHelper->render('spots/input.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spots invoeren',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function feedbackAction(string $id_list): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_NEW);

        $idArray = \array_filter(\explode('/', $id_list));
        $spots = [];
        foreach ($idArray as $id) {
            $spot = $this->formHelper->getDoctrine()->getRepository(Spot::class)->find($id);
            if (null === $spot || $spot->user !== $this->userHelper->getUser()) {
                throw new AccessDeniedException('This spot does not exist or does not belong to the user');
            }

            $spots[] = $spot;
        }

        return $this->templateHelper->render('spots/feedback.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Jouw ingevoerde spots',
            'spots' => $spots,
        ]);
    }
}
