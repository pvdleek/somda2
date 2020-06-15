<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\TrainTableYear;
use App\Helpers\FlashHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @var FlashHelper
     */
    private FlashHelper $flashHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param TemplateHelper $templateHelper
     * @param RedirectHelper $redirectHelper
     * @param FlashHelper $flashHelper
     */
    public function __construct(
        ManagerRegistry $doctrine,
        TemplateHelper $templateHelper,
        RedirectHelper $redirectHelper,
        FlashHelper $flashHelper
    ) {
        $this->doctrine = $doctrine;
        $this->templateHelper = $templateHelper;
        $this->redirectHelper = $redirectHelper;
        $this->flashHelper = $flashHelper;
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
        $trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->findTrainTableYearByDate($checkDate);

        return $this->redirectHelper->redirectToRoute(
            'train_table_search',
            ['trainTableYearId' => $trainTableYear->getId(), 'routeNumber' => $routeNumber]
        );
    }

    /**
     * @IsGranted("ROLE_SPOTS_RECENT")
     * @param int $maxYears
     * @param string|null $searchParameters
     * @return Response
     */
    public function indexAction(int $maxYears = 1, string $searchParameters = null): Response
    {
        $location = $trainNumber = $routeNumber = null;
        $dayNumber = 0;
        $spotDate = null;
        $spots = null;

        if (!is_null($searchParameters)) {
            $parameters = explode('/', $searchParameters);
            $location = strlen($parameters[0]) > 0 ? $parameters[0] : null;
            $dayNumber = (int)$parameters[1];
            try {
                $spotDate = strlen($parameters[2] > 0) ? DateTime::createFromFormat('d-m-Y', $parameters[2]) : null;
            } catch (Exception $exception) {
                $spotDate = null;
            }
            $trainNumber = strlen($parameters[3]) > 0 ? $parameters[3] : null;
            $routeNumber = strlen($parameters[4]) > 0 ? $parameters[4] : null;

            if (is_null($location) && $dayNumber === 0 && is_null($spotDate)
                && is_null($trainNumber) && is_null($routeNumber)
            ) {
                $this->flashHelper->add(
                    FlashHelper::FLASH_TYPE_WARNING,
                    'Het is niet mogelijk om spots te bekijken zonder filter, kies minimaal 1 filter'
                );
            } else {
                $spots = $this->doctrine
                    ->getRepository(Spot::class)
                    ->findWithFilters($maxYears, $location, $dayNumber, $spotDate, $trainNumber, $routeNumber);
            }
        }

        return $this->templateHelper->render('spots/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Recente spots',
            'maxYears' => $maxYears,
            'location' => $location,
            TemplateHelper::PARAMETER_DAY_NUMBER => $dayNumber,
            'spotDate' => !is_null($spotDate) ? $spotDate->format('d-m-Y') : null,
            'trainNumber' => $trainNumber,
            'routeNumber' => $routeNumber,
            'spots' => $spots,
        ]);
    }
}
