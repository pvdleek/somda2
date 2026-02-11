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

        $is_administrator = $this->user_helper->getUser()->hasRole('ROLE_ADMIN_TRAIN_COMPOSITIONS');

        if (0 === $id && null !== $type_id && $is_administrator) {
            $train_compositionType = $this->form_helper
                ->getDoctrine()
                ->getRepository(TrainCompositionType::class)
                ->find($type_id);
            if (null === $train_compositionType) {
                throw new AccessDeniedException('This trainCompositionType does not exist');
            }

            $train_composition = new TrainComposition();
            $train_composition->type = $train_compositionType;

            $this->form_helper->getDoctrine()->getManager()->persist($train_composition);
        } else {
            /** @var TrainComposition|null $train_composition */
            $train_composition = $this->form_helper->getDoctrine()->getRepository(TrainComposition::class)->find($id);
            if (null === $train_composition) {
                throw new AccessDeniedException('This trainComposition does not exist');
            }
        }

        if ($is_administrator) {
            return $this->editAsManager($request, $train_composition);
        }

        return $this->editAsUser($request, $train_composition);
    }

    /**
     * @throws \Exception
     */
    private function editAsManager(Request $request, TrainComposition $train_composition): Response|RedirectResponse
    {
        $form = $this->form_helper->getFactory()->create(
            TrainCompositionForm::class,
            $train_composition,
            [TrainCompositionForm::OPTION_MANAGEMENT_ROLE => true]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->form_helper->finishFormHandling(
                'Materieelsamenstelling bijgewerkt',
                RouteGenerics::TRAIN_COMPOSITIONS_TYPE,
                ['type_id' => $train_composition->getType()->id]
            );
        }

        return $this->template_helper->render('train/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk trein-samenstelling',
            'trainComposition' => $train_composition,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    private function editAsUser(Request $request, TrainComposition $train_composition): Response|RedirectResponse
    {
        $train_proposition = $this->form_helper
            ->getDoctrine()
            ->getRepository(TrainCompositionProposition::class)
            ->findOneBy(['composition' => $train_composition, 'user' => $this->user_helper->getUser()]);
        if (null === $train_proposition) {
            $train_proposition = new TrainCompositionProposition();
            $train_proposition->setFromTrainComposition($train_composition);
            $train_proposition->user = $this->user_helper->getUser();
        }

        $train_proposition->timestamp = new \DateTime();

        $form = $this->form_helper->getFactory()->create(TrainCompositionForm::class, $train_proposition);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->form_helper->getDoctrine()->getManager()->persist($train_proposition);
            $train_composition->addProposition($train_proposition);

            return $this->form_helper->finishFormHandling(
                'Je voorstel is ingediend. Na goedkeuring door 1 van de beheerders wordt het overzicht aangepast',
                RouteGenerics::TRAIN_COMPOSITIONS_TYPE,
                ['type_id' => $train_composition->getType()->id]
            );
        }

        return $this->template_helper->render('train/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk trein-samenstelling',
            'trainComposition' => $train_composition,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function checkAction(int $train_id, int $user_id, int $approved): JsonResponse
    {
        /** @var TrainComposition|null $train_composition */
        $train_composition = $this->form_helper->getDoctrine()->getRepository(TrainComposition::class)->find($train_id);
        if (null === $train_composition) {
            throw new AccessDeniedException('This trainComposition does not exist');
        }

        if (null === ($user = $this->form_helper->getDoctrine()->getRepository(User::class)->find($user_id))) {
            throw new AccessDeniedException('This user does not exist');
        }

        /** @var TrainCompositionProposition|null $train_proposition */
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
