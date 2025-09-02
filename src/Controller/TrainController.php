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
use App\Repository\TrainRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TrainController
{
    public function __construct(
        private readonly FormHelper $form_helper,
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
    ) {
    }

    public function indexAction(?int $type_id = null): Response
    {
        $type = null;
        $trains = [];
        if (null !== $type_id) {
            $type = $this->form_helper->getDoctrine()->getRepository(TrainCompositionType::class)->find($type_id);
            if (null === $type) {
                throw new AccessDeniedException('This trainCompositionType does not exist');
            }

            $trains = $this->form_helper->getDoctrine()->getRepository(TrainComposition::class)->findBy(
                ['type' => $type],
                ['id' => 'ASC']
            );
        }

        return $this->template_helper->render('train/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Materieel-samenstellingen',
            'types' => $this->form_helper->getDoctrine()->getRepository(TrainCompositionType::class)->findAll(),
            'selectedType' => $type,
            'trains' => $trains,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function editAction(Request $request, int $id, ?int $type_id = null): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_USER);

        $isAdministrator = $this->user_helper->getUser()->hasRole('ROLE_ADMIN_TRAIN_COMPOSITIONS');

        if ($id === 0 && null !== $type_id && $isAdministrator) {
            $trainCompositionType = $this->form_helper
                ->getDoctrine()
                ->getRepository(TrainCompositionType::class)
                ->find($type_id);
            if (null === $trainCompositionType) {
                throw new AccessDeniedException('This trainCompositionType does not exist');
            }

            $trainComposition = new TrainComposition();
            $trainComposition->type = $trainCompositionType;

            $this->form_helper->getDoctrine()->getManager()->persist($trainComposition);
        } else {
            /**
             * @var TrainComposition $trainComposition
             */
            $trainComposition = $this->form_helper->getDoctrine()->getRepository(TrainComposition::class)->find($id);
            if (null === $trainComposition) {
                throw new AccessDeniedException('This trainComposition does not exist');
            }
        }

        if ($isAdministrator) {
            return $this->editAsManager($request, $trainComposition);
        }
        return $this->editAsUser($request, $trainComposition);
    }

    /**
     * @throws \Exception
     */
    private function editAsManager(Request $request, TrainComposition $trainComposition): Response|RedirectResponse
    {
        $form = $this->form_helper->getFactory()->create(
            TrainCompositionForm::class,
            $trainComposition,
            [TrainCompositionForm::OPTION_MANAGEMENT_ROLE => true]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->form_helper->finishFormHandling(
                'Materieelsamenstelling bijgewerkt',
                RouteGenerics::TRAIN_COMPOSITIONS_TYPE,
                ['type_id' => $trainComposition->getType()->id]
            );
        }

        return $this->template_helper->render('train/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk trein-samenstelling',
            'trainComposition' => $trainComposition,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    private function editAsUser(Request $request, TrainComposition $trainComposition): Response|RedirectResponse
    {
        $trainProposition = $this->form_helper
            ->getDoctrine()
            ->getRepository(TrainCompositionProposition::class)
            ->findOneBy(['composition' => $trainComposition, 'user' => $this->user_helper->getUser()]);
        if (null === $trainProposition) {
            $trainProposition = new TrainCompositionProposition();
            $trainProposition->setFromTrainComposition($trainComposition);
            $trainProposition->user = $this->user_helper->getUser();
        }

        $trainProposition->timestamp = new \DateTime();

        $form = $this->form_helper->getFactory()->create(TrainCompositionForm::class, $trainProposition);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->form_helper->getDoctrine()->getManager()->persist($trainProposition);
            $trainComposition->addProposition($trainProposition);

            return $this->form_helper->finishFormHandling(
                'Je voorstel is ingediend. Na goedkeuring door 1 van de beheerders wordt het overzicht aangepast',
                RouteGenerics::TRAIN_COMPOSITIONS_TYPE,
                ['type_id' => $trainComposition->getType()->id]
            );
        }

        return $this->template_helper->render('train/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk trein-samenstelling',
            'trainComposition' => $trainComposition,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function checkAction(int $train_id, int $user_id, int $approved): JsonResponse
    {
        /**
         * @var TrainComposition $train_composition
         */
        $train_composition = $this->form_helper->getDoctrine()->getRepository(TrainComposition::class)->find($train_id);
        if (null === $train_composition) {
            throw new AccessDeniedException('This trainComposition does not exist');
        }

        $user = $this->form_helper->getDoctrine()->getRepository(User::class)->find($user_id);
        if (null === $user) {
            throw new AccessDeniedException('This user does not exist');
        }

        /**
         * @var TrainCompositionProposition $train_proposition
         */
        $train_proposition = $this->form_helper
            ->getDoctrine()
            ->getRepository(TrainCompositionProposition::class)
            ->findOneBy(['composition' => $train_composition, 'user' => $user]);
        if (null === $train_proposition) {
            throw new AccessDeniedException('This trainCompositionProposition does not exist');
        }

        if ($approved === 1) {
            for ($car = 1; $car <= TrainComposition::NUMBER_OF_CARS; ++$car) {
                $train_composition->{'car'.$car} = $train_proposition->{'car'.$car};
            }
            $train_composition->note = $train_proposition->note;
            $train_composition->last_update_timestamp = $train_proposition->timestamp;

            $this->form_helper->getDoctrine()->getManager()->remove($train_proposition);
            $this->form_helper->getDoctrine()->getManager()->flush();

            return new JsonResponse();
        }

        $this->form_helper->getDoctrine()->getManager()->remove($train_proposition);
        $this->form_helper->getDoctrine()->getManager()->flush();

        return new JsonResponse();
    }

    public function namesAction(): Response
    {
        /** @var TrainRepository $train_repository */
        $train_repository = $this->form_helper->getDoctrine()->getRepository(Train::class);
        $trains = $train_repository->findByTransporter();
        $transporters = [];
        foreach ($trains as $train) {
            $transporters[$train['transporter_id']] = $train['transporter_name'];
        }

        return $this->template_helper->render('train/names.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Materieelnamen',
            'transporters' => $transporters,
            'trains' => $trains,
        ]);
    }
}
