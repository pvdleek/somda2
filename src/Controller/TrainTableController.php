<?php

namespace App\Controller;

use App\Entity\SpecialRoute;
use App\Entity\TrainTableYear;
use App\Helpers\Controller\TrainTableHelper;
use App\Helpers\FlashHelper;
use App\Helpers\TemplateHelper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
     * @var FlashHelper
     */
    private FlashHelper $flashHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TemplateHelper $templateHelper
     * @param TrainTableHelper $trainTableHelper
     * @param FlashHelper $flashHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        TemplateHelper $templateHelper,
        TrainTableHelper $trainTableHelper,
        FlashHelper $flashHelper
    ) {
        $this->doctrine = $doctrine;
        $this->templateHelper = $templateHelper;
        $this->trainTableHelper = $trainTableHelper;
        $this->flashHelper = $flashHelper;
    }

    /**
     * @param int|null $trainTableIndexNumber
     * @param string|null $routeNumber
     * @return Response
     * @throws Exception
     */
    public function indexAction(int $trainTableIndexNumber = null, string $routeNumber = null): Response
    {
        $submit = false;

        if (is_null($trainTableIndexNumber)) {
            $trainTableIndexNumber =
                $this->doctrine->getRepository(TrainTableYear::class)->findCurrentTrainTableYear()->getId();
        } else {
            $submit = true;
            $this->trainTableHelper->setTrainTableYear($trainTableIndexNumber);
            $this->trainTableHelper->setRoute($routeNumber);
        }

        $trainTableLines = $submit ? $this->trainTableHelper->getTrainTableLines() : [];
        $routePredictions = $submit ? $this->trainTableHelper->getRoutePredictions() : [];
        foreach ($this->trainTableHelper->getErrorMessages() as $message) {
            $this->flashHelper->add(FlashHelper::FLASH_TYPE_ERROR, $message);
        }

        return $this->templateHelper->render('trainTable/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Dienstregeling van een trein',
            'trainTableIndices' => $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            'trainTableIndexNumber' => $trainTableIndexNumber,
            'routeNumber' => $routeNumber,
            'trainTableLines' => $trainTableLines,
            'routePredictions' => $routePredictions,
        ]);
    }

    /**
     * @param int|null $trainTableIndexNumber
     * @param string|null $locationName
     * @param int|null $dayNumber
     * @param string|null $startTime
     * @param string|null $endTime
     * @return Response
     * @throws Exception
     */
    public function passingRoutesAction(
        int $trainTableIndexNumber = null,
        string $locationName = null,
        int $dayNumber = null,
        string $startTime = null,
        string $endTime = null
    ): Response {
        $passingRoutes = [];
        if (is_null($trainTableIndexNumber)) {
            $trainTableIndexNumber =
                $this->doctrine->getRepository(TrainTableYear::class)->findCurrentTrainTableYear()->getId();
        } else {
            $this->trainTableHelper->setTrainTableYear($trainTableIndexNumber);
            $this->trainTableHelper->setLocation($locationName);
            $passingRoutes = $this->trainTableHelper->getPassingRoutes($dayNumber, $startTime, $endTime);
        }
        foreach ($this->trainTableHelper->getErrorMessages() as $message) {
            $this->flashHelper->add(FlashHelper::FLASH_TYPE_ERROR, $message);
        }

        return $this->templateHelper->render('trainTable/passingRoutes.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Doorkomststaat',
            'trainTableIndices' => $this->doctrine->getRepository(TrainTableYear::class)->findAll(),
            'trainTableIndexNumber' => $trainTableIndexNumber,
            'trainTableIndex' => $this->trainTableHelper->getTrainTableYear(),
            'locationName' => $locationName,
            'dayNumber' => $dayNumber,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'passingRoutes' => $passingRoutes,
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
            $specialRoutes = $this->doctrine->getRepository(SpecialRoute::class)->findBy(
                [], ['startDate' => 'DESC']
            );
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
