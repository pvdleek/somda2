<?php

namespace App\Controller;

use App\Entity\SpecialRoute;
use App\Entity\TrainTableYear;
use App\Helpers\TrainTableHelper;
use App\Helpers\FlashHelper;
use App\Helpers\RoutesDisplayHelper;
use App\Helpers\TemplateHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

class TrainTableController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var TemplateHelper
     */
    private TemplateHelper $templateHelper;

    /**
     * @var TrainTableHelper
     */
    private TrainTableHelper $trainTableHelper;

    /**
     * @var RoutesDisplayHelper
     */
    private RoutesDisplayHelper $routesDisplayHelper;

    /**
     * @var FlashHelper
     */
    private FlashHelper $flashHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TemplateHelper $templateHelper
     * @param TrainTableHelper $trainTableHelper
     * @param RoutesDisplayHelper $routesDisplayHelper
     * @param FlashHelper $flashHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        TemplateHelper $templateHelper,
        TrainTableHelper $trainTableHelper,
        RoutesDisplayHelper $routesDisplayHelper,
        FlashHelper $flashHelper
    ) {
        $this->doctrine = $doctrine;
        $this->templateHelper = $templateHelper;
        $this->trainTableHelper = $trainTableHelper;
        $this->routesDisplayHelper = $routesDisplayHelper;
        $this->flashHelper = $flashHelper;
    }

    /**
     * @param int|null $trainTableYearId
     * @param string|null $routeNumber
     * @return Response
     * @throws Exception
     */
    public function indexAction(int $trainTableYearId = null, string $routeNumber = null): Response
    {
        $submit = false;

        if (is_null($trainTableYearId)) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime())
                ->getId();
        } else {
            $submit = true;
            $this->trainTableHelper->setTrainTableYear($trainTableYearId);
            $this->trainTableHelper->setRoute($routeNumber);
        }

        $trainTableLines = $submit ? $this->trainTableHelper->getTrainTableLines() : [];
        $routePredictions = $submit ? $this->trainTableHelper->getRoutePredictions() : [];
        foreach ($this->trainTableHelper->getErrorMessages() as $message) {
            $this->flashHelper->add(FlashHelper::FLASH_TYPE_ERROR, $message);
        }

        return $this->templateHelper->render('trainTable/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Dienstregeling van een trein',
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDICES =>
                $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $trainTableYearId,
            'routeNumber' => $routeNumber,
            'trainTableLines' => $trainTableLines,
            'routePredictions' => $routePredictions,
        ]);
    }

    /**
     * @IsGranted("ROLE_PASSING_ROUTES")
     * @param int|null $trainTableYearId
     * @param string|null $locationName
     * @param int|null $dayNumber
     * @param string|null $startTime
     * @param string|null $endTime
     * @return Response
     * @throws Exception
     */
    public function passingRoutesAction(
        int $trainTableYearId = null,
        string $locationName = null,
        int $dayNumber = null,
        string $startTime = null,
        string $endTime = null
    ): Response {
        if (is_null($dayNumber)) {
            $trainTableYearId = $this->doctrine
                ->getRepository(TrainTableYear::class)
                ->findTrainTableYearByDate(new DateTime())
                ->getId();

            $dayNumber = date('N') - 1;
            $startTime = date('H:i', time() - (60 * 15));
            $endTime = date('H:i', time() + (60 * 45));

            $passingRoutes = [];
        } else {
            if ($trainTableYearId === 0) {
                $trainTableYearId = $this->doctrine
                    ->getRepository(TrainTableYear::class)
                    ->findTrainTableYearByDate(new DateTime())
                    ->getId();
            }

            $this->trainTableHelper->setTrainTableYear($trainTableYearId);
            $this->trainTableHelper->setLocation($locationName);

            $passingRoutes = $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime, $endTime);
        }

        foreach ($this->trainTableHelper->getErrorMessages() as $message) {
            $this->flashHelper->add(FlashHelper::FLASH_TYPE_ERROR, $message);
        }

        return $this->templateHelper->render('trainTable/passingRoutes.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Doorkomststaat',
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDICES =>
                $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $trainTableYearId,
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX => $this->trainTableHelper->getTrainTableYear(),
            'locationName' => $locationName,
            TemplateHelper::PARAMETER_DAY_NUMBER => $dayNumber,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'passingRoutes' => $passingRoutes,
        ]);
    }

    /**
     * @IsGranted("ROLE_PASSING_ROUTES")
     * @param int $trainTableYearId
     * @param string $locationName
     * @param int $dayNumber
     * @param string $startTime
     * @param string $endTime
     * @param int $spotterVersion
     * @throws Exception
     */
    public function passingRoutesExportAction(
        int $trainTableYearId,
        string $locationName,
        int $dayNumber,
        string $startTime,
        string $endTime,
        int $spotterVersion
    ) {
        $this->trainTableHelper->setTrainTableYear($trainTableYearId);
        $this->trainTableHelper->setLocation($locationName);

        $passingRoutes = $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime, $endTime);

        $html = $this->templateHelper->render('trainTable/passingRoutesExport.html.twig', [
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $trainTableYearId,
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX => $this->trainTableHelper->getTrainTableYear(),
            'locationName' => $locationName,
            TemplateHelper::PARAMETER_DAY_NUMBER => $dayNumber,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'passingRoutes' => $passingRoutes,
            'spotterVersion' => $spotterVersion === 1,
        ])->getContent();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);

        $domPdf = new Dompdf($pdfOptions);
        $domPdf->loadHtml($html);

        $domPdf->setPaper('A4', 'landscape');
        $domPdf->render();
        $domPdf->stream('doorkomststaat.pdf', ['attachment' => true]);
    }

    /**
     * @param int|null $trainTableYearId
     * @param int|null $routeListId
     * @return Response
     */
    public function routeOverviewAction(int $trainTableYearId = null, int $routeListId = null): Response
    {
        $routesDisplay = $this->routesDisplayHelper->getRoutesDisplay($trainTableYearId, $routeListId);

        return $this->templateHelper->render('trainTable/routeOverview.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Overzicht treinnummers',
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDICES =>
                $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            TemplateHelper::PARAMETER_TRAIN_TABLE_INDEX_NUMBER => $routesDisplay->trainTableYear->getId(),
            'routeLists' => $routesDisplay->routeLists,
            'selectedRouteList' => $routesDisplay->selectedRouteList,
            'routes' => $routesDisplay->routes,
        ]);
    }

    /**
     * @param int|null $id
     * @return Response
     */
    public function specialRoutesAction(int $id = null): Response
    {
        $specialRoute = null;
        if (!is_null($id)) {
            $specialRoute = $this->doctrine->getRepository(SpecialRoute::class)->find($id);
        }

        if (is_null($specialRoute)) {
            $specialRoutes = $this->doctrine
                ->getRepository(SpecialRoute::class)
                ->findBy([], ['startDate' => 'DESC']);
            return $this->templateHelper->render('trainTable/specialRoutes.html.twig', [
                TemplateHelper::PARAMETER_PAGE_TITLE => 'Bijzondere ritten',
                'specialRoutes' => $specialRoutes,
            ]);
        }

        return $this->templateHelper->render('trainTable/specialRoute.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Bijzondere rit ' . $specialRoute->title,
            'specialRoute' => $specialRoute,
        ]);
    }
}
