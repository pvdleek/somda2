<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\TrainTableYear;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SpotController
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
     * @var RedirectHelper
     */
    private RedirectHelper $redirectHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TemplateHelper $templateHelper
     * @param RedirectHelper $redirectHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        TemplateHelper $templateHelper,
        RedirectHelper $redirectHelper
    ) {
        $this->doctrine = $doctrine;
        $this->templateHelper = $templateHelper;
        $this->redirectHelper = $redirectHelper;
    }

    /**
     * @param string $routeNumber
     * @param string $date
     * @return RedirectResponse
     * @throws Exception
     */
    public function redirectToTrainTableAction(string $routeNumber, string $date): RedirectResponse
    {
        $checkDate = new DateTime($date);
        /**
         * @var TrainTableYear[] $trainTableYears
         */
        $trainTableYears = $this->doctrine->getRepository(TrainTableYear::class)->findAll();
        foreach ($trainTableYears as $trainTableYear) {
            if ($trainTableYear->startDate <= $checkDate && $trainTableYear->endDate >= $checkDate) {
                return $this->redirectHelper->redirectToRoute(
                    'train_table_search',
                    ['trainTableIndexNumber' => $trainTableYear->getId(), 'routeNumber' => $routeNumber]
                );
            }
        }
        return $this->redirectHelper->redirectToRoute(
            'train_table_search',
            ['trainTableIndexNumber' => $trainTableYears[0]->getId(), 'routeNumber' => $routeNumber]
        );
    }

    /**
     * @param int $maxYears
     * @param string|null $searchParameters
     * @return Response
     */
    public function indexAction(int $maxYears = 1, string $searchParameters = null): Response
    {
        $location = $trainNumber = $routeNumber = null;
        $dayNumber = 0;
        $spots = null;

        if (!is_null($searchParameters)) {
            $parameters = explode('/', $searchParameters);
            $location = strlen($parameters[0]) > 0 ? $parameters[0] : null;
            $dayNumber = (int)$parameters[1];
            $trainNumber = strlen($parameters[2]) > 0 ? $parameters[2] : null;
            $routeNumber = strlen($parameters[3]) > 0 ? $parameters[3] : null;

            $spots = $this->doctrine
                ->getRepository(Spot::class)
                ->findWithFilters($maxYears, $location, $dayNumber, $trainNumber, $routeNumber);
        }

        return $this->templateHelper->render('spots/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Recente spots',
            'maxYears' => $maxYears,
            'location' => $location,
            'dayNumber' => $dayNumber,
            'trainNumber' => $trainNumber,
            'routeNumber' => $routeNumber,
            'spots' => $spots,
        ]);
    }
}
