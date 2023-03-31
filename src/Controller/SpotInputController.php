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
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SpotInputController
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
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var SpotInputHelper
     */
    private SpotInputHelper $spotInputHelper;

    /**
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     * @param UserHelper $userHelper
     * @param SpotInputHelper $spotInputHelper
     */
    public function __construct(
        FormHelper $formHelper,
        TemplateHelper $templateHelper,
        UserHelper $userHelper,
        SpotInputHelper $spotInputHelper
    ) {
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
        $this->userHelper = $userHelper;
        $this->spotInputHelper = $spotInputHelper;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function indexAction(Request $request)
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
                    ['idList' => \implode('/', $spotIdArray)]
                );
            }

            return $this->formHelper->finishFormHandling('Er konden geen spots worden opgeslagen', 'spot_input');
        }

        return $this->templateHelper->render('spots/input.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Spots invoeren',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param string $idList
     * @return Response
     */
    public function feedbackAction(string $idList): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_NEW);

        $idArray = \array_filter(\explode('/', $idList));
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
