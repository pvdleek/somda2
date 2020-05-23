<?php

namespace App\Controller;

use App\Entity\TrainComposition;
use App\Entity\TrainCompositionProposition;
use App\Entity\TrainCompositionType;
use App\Entity\User;
use App\Form\TrainComposition as TrainCompositionForm;
use App\Generics\RouteGenerics;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TrainController
{
    /**
     * @var FormHelper
     */
    private FormHelper $formHelper;

    /**
     * @var UserHelper
     */
    private UserHelper $userHelper;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @param FormHelper $formHelper
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(FormHelper $formHelper, UserHelper $userHelper, TemplateHelper $templateHelper)
    {
        $this->formHelper = $formHelper;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @param int|null $typeId
     * @return Response
     */
    public function indexAction(?int $typeId = null): Response
    {
        $type = null;
        $trains = [];
        if (!is_null($typeId)) {
            $type = $this->formHelper->getDoctrine()->getRepository(TrainCompositionType::class)->find($typeId);
            if (is_null($type)) {
                throw new AccessDeniedHttpException();
            }

            $trains = $this->formHelper->getDoctrine()->getRepository(TrainComposition::class)->findBy(
                ['type' => $type]
            );
        }

        return $this->templateHelper->render('train/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Materieel-samenstellingen',
            'types' => $this->formHelper->getDoctrine()->getRepository(TrainCompositionType::class)->findAll(),
            'selectedType' => $type,
            'trains' => $trains,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function editAction(Request $request, int $id)
    {
        /**
         * @var TrainComposition $trainComposition
         */
        $trainComposition = $this->formHelper->getDoctrine()->getRepository(TrainComposition::class)->find($id);
        if (is_null($trainComposition)) {
            throw new AccessDeniedHttpException();
        }

        if ($this->userHelper->getUser()->hasRole('ROLE_ADMIN_TRAIN_COMPOSITIONS')) {
            return $this->editAsManager($request, $trainComposition);
        }
        return $this->editAsUser($request, $trainComposition);
    }

    /**
     * @param Request $request
     * @param TrainComposition $trainComposition
     * @return RedirectResponse|Response
     */
    private function editAsManager(Request $request, TrainComposition $trainComposition)
    {
        $form = $this->formHelper->getFactory()->create(
            TrainCompositionForm::class,
            $trainComposition,
            [TrainCompositionForm::OPTION_MANAGEMENT_ROLE => true]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formHelper->finishFormHandling(
                'Materieelsamenstelling bijgewerkt',
                RouteGenerics::TRAIN_COMPOSITIONS_TYPE,
                ['typeId' => $trainComposition->getType()->getId()]
            );
        }

        return $this->templateHelper->render('train/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk trein-samenstelling',
            'trainComposition' => $trainComposition,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param TrainComposition $trainComposition
     * @return RedirectResponse|Response
     */
    private function editAsUser(Request $request, TrainComposition $trainComposition)
    {
        $trainCompositionProposition = $this->formHelper
            ->getDoctrine()
            ->getRepository(TrainCompositionProposition::class)
            ->findOneBy(['composition' => $trainComposition, 'user' => $this->userHelper->getUser()]);
        if (is_null($trainCompositionProposition)) {
            $trainCompositionProposition = new TrainCompositionProposition();
            $trainCompositionProposition->setFromTrainComposition($trainComposition);
            $trainCompositionProposition->user = $this->userHelper->getUser();
        }

        $trainCompositionProposition->timestamp = new DateTime();

        $form = $this->formHelper->getFactory()->create(TrainCompositionForm::class, $trainCompositionProposition);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formHelper->getDoctrine()->getManager()->persist($trainCompositionProposition);
            $trainComposition->addProposition($trainCompositionProposition);

            return $this->formHelper->finishFormHandling(
                'Je voorstel is ingediend. Na goedkeuring door 1 van de beheerders wordt het overzicht aangepast',
                RouteGenerics::TRAIN_COMPOSITIONS_TYPE,
                ['typeId' => $trainComposition->getType()->getId()]
            );
        }

        return $this->templateHelper->render('train/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk trein-samenstelling',
            'trainComposition' => $trainComposition,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param int $trainId
     * @param int $userId
     * @param int $approved
     * @return JsonResponse
     */
    public function checkAction(int $trainId, int $userId, int $approved): JsonResponse
    {
        /**
         * @var TrainComposition $trainComposition
         * @var TrainCompositionProposition $trainCompositionProposition
         */
        $trainComposition = $this->formHelper->getDoctrine()->getRepository(TrainComposition::class)->find($trainId);
        if (is_null($trainComposition)) {
            throw new AccessDeniedHttpException();
        }

        $user = $this->formHelper->getDoctrine()->getRepository(User::class)->find($userId);
        if (is_null($user)) {
            throw new AccessDeniedHttpException();
        }

        $trainCompositionProposition = $this->formHelper
            ->getDoctrine()
            ->getRepository(TrainCompositionProposition::class)
            ->findOneBy(['composition' => $trainComposition, 'user' => $user]);
        if (is_null($trainCompositionProposition)) {
            throw new AccessDeniedHttpException();
        }

        if ($approved === 1) {
            for ($car = 1; $car <= TrainComposition::NUMBER_OF_CARS; ++$car) {
                $trainComposition->{'car' . $car} = $trainCompositionProposition->{'car' . $car};
            }
            $trainComposition->note = $trainCompositionProposition->note;

            $this->formHelper->getDoctrine()->getManager()->remove($trainCompositionProposition);
            $this->formHelper->getDoctrine()->getManager()->flush();

            return new JsonResponse();
        }

        $this->formHelper->getDoctrine()->getManager()->remove($trainCompositionProposition);
        $this->formHelper->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }
}
