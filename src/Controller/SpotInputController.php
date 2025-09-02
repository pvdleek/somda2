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
        private readonly FormHelper $form_helper,
        private readonly SpotInputHelper $spot_input_helper,
        private readonly TemplateHelper $template_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function indexAction(Request $request): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_NEW);

        $form = $this->form_helper->getFactory()->create(
            SpotBulk::class,
            null,
            ['defaultLocation' => $this->user_helper->getDefaultLocation()]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $spot_lines = \array_filter(\preg_split('/$\R?^/m', $form->get('spots')->getData()));
            $spot_id_array = $this->spot_input_helper->processSpotLines(
                $spot_lines,
                $this->user_helper->getUser(),
                $form->get('date')->getData(),
                $form->get('location')->getData()
            );

            if (\count($spot_id_array) > 0) {
                return $this->form_helper->finishFormHandling(
                    'Spot(s) opgeslagen',
                    'spot_input_feedback',
                    ['id_list' => \implode('/', $spot_id_array)]
                );
            }

            return $this->form_helper->finishFormHandling('Er konden geen spots worden opgeslagen', 'spot_input');
        }

        return $this->template_helper->render('spots/input.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spots invoeren',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function feedbackAction(string $id_list): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_NEW);

        $spots = [];
        foreach (\array_filter(\explode('/', $id_list)) as $id) {
            $spot = $this->form_helper->getDoctrine()->getRepository(Spot::class)->find($id);
            if (null === $spot || $spot->user !== $this->user_helper->getUser()) {
                throw new AccessDeniedException('This spot does not exist or does not belong to the user');
            }

            $spots[] = $spot;
        }

        return $this->template_helper->render('spots/feedback.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Jouw ingevoerde spots',
            'spots' => $spots,
        ]);
    }
}
