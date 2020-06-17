<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Form\Spot as SpotForm;
use App\Helpers\FormHelper;
use App\Helpers\SpotInputHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Model\DataTableOrder;
use App\Model\SpotInput;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MySpotsController
{
    private const COLUMN_DATA = 'data';
    private const COLUMN_SEARCH = 'search';
    private const COLUMN_SEARCH_VALUE = 'value';

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
     * @var SpotInputHelper
     */
    private SpotInputHelper $spotInputHelper;

    /**
     * @param FormHelper $formHelper
     * @param UserHelper $userHelper
     * @param TemplateHelper $templateHelper
     * @param SpotInputHelper $spotInputHelper
     */
    public function __construct(
        FormHelper $formHelper,
        UserHelper $userHelper,
        TemplateHelper $templateHelper,
        SpotInputHelper $spotInputHelper
    ) {
        $this->formHelper = $formHelper;
        $this->userHelper = $userHelper;
        $this->templateHelper = $templateHelper;
        $this->spotInputHelper = $spotInputHelper;
    }

    /**
     * @IsGranted("ROLE_SPOTS_EDIT")
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->templateHelper->render('spots/mySpots.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Mijn spots',
        ]);
    }

    /**
     * @IsGranted("ROLE_SPOTS_EDIT")
     * @param Request $request
     * @return JsonResponse
     */
    public function jsonAction(Request $request): JsonResponse
    {
        $columns = $request->get('columns');

        $dateSearch = $locationSearch = $trainNumberSearch = $routeNumberSearch = null;
        foreach ($columns as $column) {
            if (strlen($column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE]) > 0) {
                if ($column[self::COLUMN_DATA] === 'spotDate') {
                    $dateSearch =
                        DateTime::createFromFormat('d-m-Y', $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE]);
                } elseif ($column[self::COLUMN_DATA] === 'location') {
                    $locationSearch = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                } elseif ($column[self::COLUMN_DATA] === 'train') {
                    $trainNumberSearch = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                } elseif ($column[self::COLUMN_DATA] === 'route') {
                    $routeNumberSearch = $column[self::COLUMN_SEARCH][self::COLUMN_SEARCH_VALUE];
                }
            }
        }

        $orderArray = $request->get('order');
        $spotOrder = [];
        foreach ($orderArray as $key => $order) {
            $spotOrder[$key] = new DataTableOrder(
                $columns[$order['column']][self::COLUMN_DATA],
                strtolower($order['dir']) === 'asc'
            );
        }

        $spots = $this->formHelper->getDoctrine()->getRepository(Spot::class)->findForMySpots(
            $this->userHelper->getUser(),
            $dateSearch,
            $locationSearch,
            $trainNumberSearch,
            $routeNumberSearch,
            (int)$request->get('length'),
            (int)$request->get('start'),
            $spotOrder
        );
        $filteredRecords = $this->formHelper->getDoctrine()->getRepository(Spot::class)->countForMySpots(
            $this->userHelper->getUser(),
            $dateSearch,
            $locationSearch,
            $trainNumberSearch,
            $routeNumberSearch
        );

        $response = [
            'draw' => $request->get('draw'),
            'recordsTotal' => $this->formHelper->getDoctrine()->getRepository(Spot::class)->countAll(
                $this->userHelper->getUser()
            ),
            'recordsFiltered' => $filteredRecords,
            self::COLUMN_DATA => [],
        ];
        foreach ($spots as $spot) {
            $response[self::COLUMN_DATA][] = $spot->toArray();
        }

        return new JsonResponse($response);
    }

    /**
     * @IsGranted("ROLE_SPOTS_EDIT")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $id)
    {
        $spot = $this->formHelper->getDoctrine()->getRepository(Spot::class)->find($id);
        if (is_null($spot) || $spot->user !== $this->userHelper->getUser()) {
            throw new AccessDeniedHttpException();
        }
        $form = $this->formHelper->getFactory()->create(SpotForm::class, $spot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $spotInput = new SpotInput();
            $spotInput->existingSpotId = $spot->getId();
            $spotInput->user = $this->userHelper->getUser();
            $spotInput->spotDate = $form->get('spotDate')->getData();
            $spotInput->trainNumber = $form->get('train')->getData();
            $spotInput->routeNumber = $form->get('route')->getData();
            $spotInput->positionId = $form->get('position')->getData()->getId();
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

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction(int $id): RedirectResponse
    {
        $spot = $this->formHelper->getDoctrine()->getRepository(Spot::class)->find($id);
        if (is_null($spot) || $spot->user !== $this->userHelper->getUser()) {
            throw new AccessDeniedHttpException();
        }

        if (!is_null($spot->extra)) {
            $this->formHelper->getDoctrine()->getManager()->remove($spot->extra);
        }
        $this->formHelper->getDoctrine()->getManager()->remove($spot);

        return $this->formHelper->finishFormHandling('Spot verwijderd', 'my_spots');
    }
}
