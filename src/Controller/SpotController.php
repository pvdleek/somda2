<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\TrainTableYear;
use App\Helpers\FlashHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Model\SpotFilter;
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
     * @param int $maxMonths
     * @param string|null $searchParameters
     * @return Response
     */
    public function indexAction(int $maxMonths = 1, string $searchParameters = null): Response
    {
        $spotFilter = new SpotFilter();
        $spots = null;

        if (!is_null($searchParameters)) {
            $spotFilter->createFromSearchParameters(explode('/', $searchParameters));

            if (!$spotFilter->isValid()) {
                $this->flashHelper->add(
                    FlashHelper::FLASH_TYPE_WARNING,
                    'Het is niet mogelijk om spots te bekijken zonder filter, kies minimaal 1 filter'
                );
            } else {
                $spots = $this->doctrine->getRepository(Spot::class)->findWithSpotFilter($maxMonths, $spotFilter);
            }
        }

        return $this->templateHelper->render('spots/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Recente spots',
            'maxMonths' => $maxMonths,
            'location' => $spotFilter->location,
            TemplateHelper::PARAMETER_DAY_NUMBER => $spotFilter->dayNumber,
            'spotDate' => !is_null($spotFilter->spotDate) ? $spotFilter->spotDate->format('d-m-Y') : null,
            'trainNumber' => $spotFilter->trainNumber,
            'routeNumber' => $spotFilter->routeNumber,
            'spots' => $spots,
        ]);
    }
}
