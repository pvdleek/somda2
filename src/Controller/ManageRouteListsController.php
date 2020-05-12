<?php

namespace App\Controller;

use App\Entity\Characteristic;
use App\Entity\RouteList;
use App\Entity\TrainTableYear;
use App\Entity\Transporter;
use App\Form\RouteList as RouteListForm;
use App\Helpers\FormHelper;
use App\Helpers\TemplateHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ManageRouteListsController
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
     * @param ManagerRegistry $doctrine
     * @param FormHelper $formHelper
     * @param TemplateHelper $templateHelper
     */
    public function __construct(ManagerRegistry $doctrine, FormHelper $formHelper, TemplateHelper $templateHelper)
    {
        $this->doctrine = $doctrine;
        $this->formHelper = $formHelper;
        $this->templateHelper = $templateHelper;
    }

    /**
     * @IsGranted("ROLE_ADMIN_ROUTE_NUMBER_LIST")
     * @param int|null $id
     * @return Response
     * @throws Exception
     */
    public function routeListsAction(int $id = null): Response
    {
        if (is_null($id)) {
            $trainTableYear = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime());
            $routeLists = [];
        } else {
            $trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->find($id);
            if (is_null($trainTableYear)) {
                throw new AccessDeniedHttpException();
            }

            $routeLists = $this->doctrine->getRepository(RouteList::class)->findBy(
                ['trainTableYear' => $trainTableYear],
                ['firstNumber' => 'ASC']
            );
        }

        return $this->templateHelper->render('manageTrainTable/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer treinnummerlijst',
            'trainTableYears' => $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findBy([], ['startDate' => 'DESC']),
            'trainTableYear' => $trainTableYear,
            'routeLists' => $routeLists,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN_ROUTE_NUMBER_LIST")
     * @param Request $request
     * @param int $yearId
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function routeListAction(Request $request, int $yearId, int $id)
    {
        $routeList = $this->getOrCreateRouteList($yearId, $id);
        $form = $this->formHelper->getFactory()->create(RouteListForm::class, $routeList);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($routeList->getId() > 0) {
                $message = 'Treinnummer bijgewerkt';
            } else {
                // New routeList
                $message = 'Treinnummer toegevoegd';
                $this->doctrine->getManager()->persist($routeList);
            }
            return $this->formHelper->finishFormHandling(
                $message,
                'manage_route_lists_year',
                ['id' => $routeList->trainTableYear->getId()]
            );
        }

        return $this->templateHelper->render('manageTrainTable/item.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Beheer treinnummerlijst',
            'routeList' => $routeList,
            TemplateHelper::PARAMETER_FORM => $form->createView(),
        ]);
    }

    /**
     * @param int $yearId
     * @param int $id
     * @return RouteList
     */
    private function getOrCreateRouteList(int $yearId, int $id): RouteList
    {
        if ($id > 0) {
            /**
             * @var RouteList $routeList
             */
            $routeList = $this->doctrine->getRepository(RouteList::class)->find($id);
            if (is_null($routeList)) {
                throw new AccessDeniedHttpException();
            }

            return $routeList;
        }

        $trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->find($yearId);
        if (is_null($trainTableYear)) {
            throw new AccessDeniedHttpException();
        }

        $routeList = new RouteList();
        $routeList->trainTableYear = $trainTableYear;
        $routeList->transporter = $this->doctrine->getRepository(Transporter::class)->findOneBy([]);
        $routeList->characteristic = $this->doctrine->getRepository(Characteristic::class)->findOneBy([]);

        return $routeList;
    }
}
