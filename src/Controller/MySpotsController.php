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
        private readonly FormHelper $formHelper,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly SpotInputHelper $spotInputHelper,
    ) {
    }

    public function indexAction(): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_EDIT);

        return $this->templateHelper->render('spots/mySpots.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Mijn spots',
        ]);
    }

    public function jsonAction(Request $request): JsonResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_EDIT);

        $columns = $request->get('columns');
        $spotFilter = $this->getSpotFilterFromRequest($columns);

        $orderArray = $request->get('order');
        $spotOrder = [];
        foreach ($orderArray as $key => $order) {
            $spotOrder[$key] = new DataTableOrder(
                $columns[$order['column']][self::COLUMN_DATA],
                \strtolower($order['dir']) === 'asc'
            );
        }

        $response = [
            'draw' => $request->get('draw'),
            'recordsTotal' => $this->formHelper->getDoctrine()->getRepository(Spot::class)->countAll(
                $this->userHelper->getUser()
            ),
            'recordsFiltered' => $this->formHelper->getDoctrine()->getRepository(Spot::class)->countForMySpots(
                $this->userHelper->getUser(),
                $spotFilter
            ),
            self::COLUMN_DATA => [],
        ];

        $spots = $this->formHelper->getDoctrine()->getRepository(Spot::class)->findForMySpots(
            $this->userHelper->getUser(),
            $spotFilter,
            (int) $request->get('length'),
            (int) $request->get('start'),
            $spotOrder
        );
        foreach ($spots as $spot) {
            $response[self::COLUMN_DATA][] = $spot->toArray();
        }

        return new JsonResponse($response);
    }

    private function getSpotFilterFromRequest(array $columns): SpotFilter
    {
        $spotFilter = new SpotFilter();
        foreach ($columns as $column) {
            if (\strlen($column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE]) > 0) {
                if ($column[self::COLUMN_DATA] === 'spotDate') {
                    $spotFilter->spotDate = \DateTime::createFromFormat(
                        'd-m-Y',
                        $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE]
                    );
                } elseif ($column[self::COLUMN_DATA] === 'location') {
                    $spotFilter->location = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                } elseif ($column[self::COLUMN_DATA] === 'train') {
                    $spotFilter->trainNumber = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                } elseif ($column[self::COLUMN_DATA] === 'route') {
                    $spotFilter->routeNumber = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                }
            }
        }

        return $spotFilter;
    }

    public function editAction(Request $request, int $id): Response|RedirectResponse
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_EDIT);

        $spot = $this->formHelper->getDoctrine()->getRepository(Spot::class)->find($id);
        if (null === $spot || $spot->user !== $this->userHelper->getUser()) {
            throw new AccessDeniedException('This spot does not exist or does not belong to the user');
        }
        $form = $this->formHelper->getFactory()->create(SpotForm::class, $spot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $spotInput = new SpotInput();
            $spotInput->existingSpotId = $spot->id;
            $spotInput->user = $this->userHelper->getUser();
            $spotInput->spotDate = $form->get('spotDate')->getData();
            $spotInput->trainNumber = $form->get('train')->getData();
            $spotInput->routeNumber = $form->get('route')->getData();
            $spotInput->positionId = $form->get('position')->getData()->id;
            $spotInput->location = $form->get('location')->getData();
            $spotInput->extra = $form->get('extra')->getData() ?? '';
            $spotInput->userExtra = $form->get('userExtra')->getData();

            $this->spotInputHelper->processSpotInput($spotInput);

            return $this->formHelper->finishFormHandling('Spot bijgewerkt', 'my_spots');
        }

        return $this->templateHelper->render('spots/edit.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk spot',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    public function deleteAction(int $id): RedirectResponse
    {
        $spot = $this->formHelper->getDoctrine()->getRepository(Spot::class)->find($id);
        if (null === $spot || $spot->user !== $this->userHelper->getUser()) {
            throw new AccessDeniedException('This spot does not exist or does not belong to the user');
        }

        if (null !== $spot->extra) {
            $this->formHelper->getDoctrine()->getManager()->remove($spot->extra);
        }
        $this->formHelper->getDoctrine()->getManager()->remove($spot);

        return $this->formHelper->finishFormHandling('Spot verwijderd', 'my_spots');
    }

    /**
     * @throws \Exception
     */
    public function bulkAction(Request $request, string $type, string $idList): Response|RedirectResponse
    {
        $idArray = \array_filter(\explode(',', $idList));

        if (self::BULK_ACTION_DATE === $type) {
            $form = $this->formHelper->getFactory()->create(SpotBulkEditDate::class);
        } elseif (self::BULK_ACTION_LOCATION === $type) {
            $form = $this->formHelper->getFactory()->create(
                SpotBulkEditLocation::class,
                null,
                ['defaultLocation' => $this->userHelper->getDefaultLocation()]
            );
        } else {
            throw new AccessDeniedHttpException();
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $spots = $this->formHelper
                ->getDoctrine()
                ->getRepository(Spot::class)
                ->findByIdsAndUser($idArray, $this->userHelper->getUser());
            foreach ($spots as $spot) {
                if (self::BULK_ACTION_DATE === $type) {
                    $spot->spotDate = $form->get('date')->getData();
                } else {
                    $spot->location = $form->get('location')->getData();
                }
            }

            $this->formHelper->getDoctrine()->getManager()->flush();
            $this->formHelper->getFlashHelper()->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Spots aangepast');

            return $this->formHelper->getRedirectHelper()->redirectToRoute('my_spots');
        }

        $spots = $this->formHelper
            ->getDoctrine()
            ->getRepository(Spot::class)
            ->findByIdsAndUserForDisplay($idArray, $this->userHelper->getUser());
        return $this->templateHelper->render('spots/editBulk.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bewerk meerdere spots',
            TemplateHelper::PARAMETER_FORM => $form->createView(),
            'spots' => $spots,
        ]);
    }
}
