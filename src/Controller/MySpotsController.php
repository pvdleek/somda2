<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Spot;
use App\Form\Spot as SpotForm;
use App\Form\SpotBulkEditDate;
use App\Form\SpotBulkEditLocation;
use App\Generics\RoleGenerics;
use App\Helpers\FlashHelper;
use App\Helpers\FormHelper;
use App\Helpers\SpotInputHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Model\DataTableOrder;
use App\Model\SpotFilter;
use App\Model\SpotInput;
use App\Repository\SpotRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MySpotsController
{
    private const COLUMN_DATA = 'data';
    private const COLUMN_SEARCH = 'search';
    private const COLUMN_SEARCH_VALUE = 'value';

    private const BULK_ACTION_DATE = 'date';
    private const BULK_ACTION_LOCATION = 'location';

    public function __construct(
        private readonly FormHelper $form_helper,
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
        private readonly SpotInputHelper $spot_input_helper,
        private readonly SpotRepository $spot_repository,
    ) {
    }

    public function indexAction(): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_EDIT);

        return $this->template_helper->render('spots/mySpots.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Mijn spots',
        ]);
    }

    public function jsonAction(Request $request): JsonResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_EDIT);

        $columns = $request->get('columns');
        $spot_filter = $this->getSpotFilterFromRequest($columns);

        $order_array = $request->get('order');
        $spot_order = [];
        foreach ($order_array as $key => $order) {
            $spot_order[$key] = new DataTableOrder(
                $columns[$order['column']][self::COLUMN_DATA],
                \strtolower($order['dir']) === 'asc'
            );
        }

        $response = [
            'draw' => $request->get('draw'),
            'recordsTotal' => $this->spot_repository->countAll($this->user_helper->getUser()),
            'recordsFiltered' => $this->spot_repository->countForMySpots($this->user_helper->getUser(), $spot_filter),
            self::COLUMN_DATA => [],
        ];

        $spots = $this->spot_repository->findForMySpots(
            $this->user_helper->getUser(),
            $spot_filter,
            (int) $request->get('length'),
            (int) $request->get('start'),
            $spot_order
        );
        foreach ($spots as $spot) {
            $response[self::COLUMN_DATA][] = $spot->toArray();
        }

        return new JsonResponse($response);
    }

    private function getSpotFilterFromRequest(array $columns): SpotFilter
    {
        $spot_filter = new SpotFilter();
        foreach ($columns as $column) {
            if (\strlen($column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE]) > 0) {
                if ($column[self::COLUMN_DATA] === 'spot_date') {
                    $spot_filter->spot_date = \DateTime::createFromFormat(
                        'd-m-Y',
                        $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE]
                    );
                } elseif ($column[self::COLUMN_DATA] === 'location') {
                    $spot_filter->location = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                } elseif ($column[self::COLUMN_DATA] === 'train') {
                    $spot_filter->train_number = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                } elseif ($column[self::COLUMN_DATA] === 'route') {
                    $spot_filter->route_number = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                }
            }
        }

        return $spot_filter;
    }

    public function editAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_EDIT);

        $spot = $this->form_helper->getDoctrine()->getRepository(Spot::class)->find($id);
        if (null === $spot || $spot->user !== $this->user_helper->getUser()) {
            throw new AccessDeniedException('This spot does not exist or does not belong to the user');
        }
        $form = $this->form_helper->getFactory()->create(SpotForm::class, $spot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $spot_input = new SpotInput();
            $spot_input->existingSpotId = $spot->id;
            $spot_input->user = $this->user_helper->getUser();
            $spot_input->spot_date = $form->get('spot_date')->getData();
            $spot_input->train_number = $form->get('train')->getData();
            $spot_input->route_number = $form->get('route')->getData();
            $spot_input->position_id = $form->get('position')->getData()->id;
            $spot_input->location = $form->get('location')->getData();
            $spot_input->extra = $form->get('extra')->getData() ?? '';
            $spot_input->user_extra = $form->get('user_extra')->getData();

            $this->spot_input_helper->processSpotInput($spot_input);

            return $this->form_helper->finishFormHandling('Spot bijgewerkt', 'my_spots');
        }

        return $this->template_helper->render('spots/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk spot',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function deleteAction(int $id): RedirectResponse
    {
        $spot = $this->form_helper->getDoctrine()->getRepository(Spot::class)->find($id);
        if (null === $spot || $spot->user !== $this->user_helper->getUser()) {
            throw new AccessDeniedException('This spot does not exist or does not belong to the user');
        }

        if (null !== $spot->extra) {
            $this->form_helper->getDoctrine()->getManager()->remove($spot->extra);
        }
        $this->form_helper->getDoctrine()->getManager()->remove($spot);

        return $this->form_helper->finishFormHandling('Spot verwijderd', 'my_spots');
    }

    /**
     * @throws \Exception
     */
    public function bulkAction(Request $request, string $type, string $id_list): Response|RedirectResponse
    {
        $id_array = \array_filter(\explode(',', $id_list));

        if (self::BULK_ACTION_DATE === $type) {
            $form = $this->form_helper->getFactory()->create(SpotBulkEditDate::class);
        } elseif (self::BULK_ACTION_LOCATION === $type) {
            $form = $this->form_helper->getFactory()->create(
                SpotBulkEditLocation::class,
                null,
                ['defaultLocation' => $this->user_helper->getDefaultLocation()]
            );
        } else {
            throw new AccessDeniedHttpException();
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $spots = $this->spot_repository->findByIdsAndUser($id_array, $this->user_helper->getUser());
            foreach ($spots as $spot) {
                if (self::BULK_ACTION_DATE === $type) {
                    $spot->spot_date = $form->get('date')->getData();
                } else {
                    $spot->location = $form->get('location')->getData();
                }
            }

            $this->form_helper->getDoctrine()->getManager()->flush();
            $this->form_helper->getFlashHelper()->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Spots aangepast');

            return $this->form_helper->getRedirectHelper()->redirectToRoute('my_spots');
        }

        $spots = $this->spot_repository->findByIdsAndUserForDisplay($id_array, $this->user_helper->getUser());
        return $this->template_helper->render('spots/editBulk.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk meerdere spots',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'spots' => $spots,
        ]);
    }
}
