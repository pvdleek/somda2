<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Train;
use App\Entity\TrainComposition;
use App\Entity\TrainCompositionProposition;
use App\Entity\TrainCompositionType;
use App\Entity\User;
use App\Form\TrainComposition as TrainCompositionForm;
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
                throw new AccessDeniedException('This trainCompositionType does not exist');
            }

            $trains = $this->formHelper->getDoctrine()->getRepository(TrainComposition::class)->findBy(
                ['type' => $type],
                ['id' => 'ASC']
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
     * @param Request $request
     * @param int $id
     * @param int|null $typeId
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction(Request $request, int $id, int $typeId = null)
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $isAdministrator = $this->userHelper->getUser()->hasRole('ROLE_ADMIN_TRAIN_COMPOSITIONS');

        /**
         * @var TrainComposition $trainComposition
         */

        if ($id === 0 && !is_null($typeId) && $isAdministrator) {
            $trainCompositionType = $this->formHelper
                ->getDoctrine()
                ->getRepository(TrainCompositionType::class)
                ->find($typeId);
            if (is_null($trainCompositionType)) {
                throw new AccessDeniedException('This trainCompositionType does not exist');
            }

            $trainComposition = new TrainComposition();
            $trainComposition->type = $trainCompositionType;

            $this->formHelper->getDoctrine()->getManager()->persist($trainComposition);
        } else {
            $trainComposition = $this->formHelper->getDoctrine()->getRepository(TrainComposition::class)->find($id);
            if (is_null($trainComposition)) {
                throw new AccessDeniedException('This trainComposition does not exist');
            }
        }

        if ($isAdministrator) {
            return $this->editAsManager($request, $trainComposition);
        }
        return $this->editAsUser($request, $trainComposition);
    }

    /**
     * @param Request $request
     * @param TrainComposition $trainComposition
     * @return RedirectResponse|Response
     * @throws \Exception
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
                ['typeId' => $trainComposition->getType()->id]
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
        $trainProposition = $this->formHelper
            ->getDoctrine()
            ->getRepository(TrainCompositionProposition::class)
            ->findOneBy(['composition' => $trainComposition, 'user' => $this->userHelper->getUser()]);
        if (is_null($trainProposition)) {
            $trainProposition = new TrainCompositionProposition();
            $trainProposition->setFromTrainComposition($trainComposition);
            $trainProposition->user = $this->userHelper->getUser();
        }

        $trainProposition->timestamp = new \DateTime();

        $form = $this->formHelper->getFactory()->create(TrainCompositionForm::class, $trainProposition);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formHelper->getDoctrine()->getManager()->persist($trainProposition);
            $trainComposition->addProposition($trainProposition);

            return $this->formHelper->finishFormHandling(
                'Je voorstel is ingediend. Na goedkeuring door 1 van de beheerders wordt het overzicht aangepast',
                RouteGenerics::TRAIN_COMPOSITIONS_TYPE,
                ['typeId' => $trainComposition->getType()->id]
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
         * @var TrainCompositionProposition $trainProposition
         */
        $trainComposition = $this->formHelper->getDoctrine()->getRepository(TrainComposition::class)->find($trainId);
        if (is_null($trainComposition)) {
            throw new AccessDeniedException('This trainComposition does not exist');
        }

        $user = $this->formHelper->getDoctrine()->getRepository(User::class)->find($userId);
        if (is_null($user)) {
            throw new AccessDeniedException('This user does not exist');
        }

        $trainProposition = $this->formHelper
            ->getDoctrine()
            ->getRepository(TrainCompositionProposition::class)
            ->findOneBy(['composition' => $trainComposition, 'user' => $user]);
        if (is_null($trainProposition)) {
            throw new AccessDeniedException('This trainCompositionProposition does not exist');
        }

        if ($approved === 1) {
            for ($car = 1; $car <= TrainComposition::NUMBER_OF_CARS; ++$car) {
                $trainComposition->{'car' . $car} = $trainProposition->{'car' . $car};
            }
            $trainComposition->note = $trainProposition->note;
            $trainComposition->lastUpdateTimestamp = $trainProposition->timestamp;

            $this->formHelper->getDoctrine()->getManager()->remove($trainProposition);
            $this->formHelper->getDoctrine()->getManager()->flush();

            return new JsonResponse();
        }

        $this->formHelper->getDoctrine()->getManager()->remove($trainProposition);
        $this->formHelper->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }

    public function namesAction(): Response
    {
        $trains = $this->formHelper->getDoctrine()->getRepository(Train::class)->findByTransporter();
        $transporters = [];
        foreach ($trains as $train) {
            $transporters[$train['transporterId']] = $train['transporterName'];
        }

        return $this->templateHelper->render('train/names.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Materieelnamen',
            'transporters' => $transporters,
            'trains' => $trains,
        ]);
    }
}
