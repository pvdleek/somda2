<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Spot;
use App\Entity\UserPreference;
use App\Form\SpotBulk;
use App\Helpers\FormHelper;
use App\Helpers\SpotInputHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SpotInputController
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
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var SpotInputHelper
     */
    private SpotInputHelper $spotInputHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     * @param UserHelper $userHelper
     * @param SpotInputHelper $spotInputHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormHelper $formHelper,
        TemplateHelper $templateHelper,
        UserHelper $userHelper,
        SpotInputHelper $spotInputHelper
    ) {
        $this->doctrine = $doctrine;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
        $this->userHelper = $userHelper;
        $this->spotInputHelper = $spotInputHelper;
    }

    /**
     * @IsGranted("ROLE_SPOTS_NEW")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        $form = $this->formHelper->getFactory()->create(
            SpotBulk::class,
            null,
            ['defaultLocation' => $this->getDefaultLocation()]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $spotLines = array_filter(preg_split('/$\R?^/m', $form->get('spots')->getData()));
            $spotIdArray = $this->spotInputHelper->processSpotLines(
                $spotLines,
                $this->userHelper->getUser(),
                $form->get('date')->getData(),
                $form->get('location')->getData()
            );

            if (count($spotIdArray) > 0) {
                return $this->formHelper->finishFormHandling(
                    'Spot(s) opgeslagen',
                    'spot_input_feedback',
                    ['idList' => implode('/', $spotIdArray)]
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
     * @return Location|null
     * @throws Exception
     */
    private function getDefaultLocation(): ?Location
    {
        $location = null;
        $defaultLocation = $this->userHelper->getPreferenceByKey(UserPreference::KEY_DEFAULT_SPOT_LOCATION);
        if (strlen($defaultLocation->value) > 0) {
            /**
             * @var Location $location
             */
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $defaultLocation->value]);
        }
        return $location;
    }

    /**
     * @IsGranted("ROLE_SPOTS_NEW")
     * @param string $idList
     * @return Response
     */
    public function feedbackAction(string $idList): Response
    {
        $idArray = array_filter(explode('/', $idList));
        $spots = [];
        foreach ($idArray as $id) {
            $spot = $this->doctrine->getRepository(Spot::class)->find($id);
            if (is_null($spot) || $spot->user !== $this->userHelper->getUser()) {
                throw new AccessDeniedHttpException();
            }

            $spots[] = $spot;
        }

        return $this->templateHelper->render('spots/feedback.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Jouw ingevoerde spots',
            'spots' => $spots,
        ]);
    }
}
