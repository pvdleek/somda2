<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\TrainTableYear;
use App\Generics\RoleGenerics;
use App\Helpers\FlashHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Model\SpotFilter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SpotController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $userHelper,
        private readonly TemplateHelper $templateHelper,
        private readonly RedirectHelper $redirectHelper,
        private readonly FlashHelper $flashHelper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function redirectToTrainTableAction(string $routeNumber, string $date): RedirectResponse
    {
        $checkDate = new \DateTime($date);
        $trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->findTrainTableYearByDate($checkDate);

        return $this->redirectHelper->redirectToRoute(
            'train_table_search',
            ['trainTableYearId' => $trainTableYear->id, 'routeNumber' => $routeNumber]
        );
    }

    public function indexAction(int $maxMonths = 1, string $searchParameters = null): Response
    {
        $this->userHelper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_RECENT);

        $trainTableYear = $this->doctrine
            ->getRepository(TrainTableYear::class)
            ->findTrainTableYearByDate(new \DateTime());

        $spotFilter = new SpotFilter();
        $spots = null;

        if (!\is_null($searchParameters)) {
            $spotFilter->createFromSearchParameters(explode('/', $searchParameters));

            if (!$spotFilter->isValid()) {
                $this->flashHelper->add(
                    FlashHelper::FLASH_TYPE_WARNING,
                    'Het is niet mogelijk om spots te bekijken zonder filter, kies minimaal 1 filter'
                );
            } else {
                $spots = $this->doctrine
                    ->getRepository(Spot::class)
                    ->findRecentWithSpotFilter($maxMonths, $spotFilter, $trainTableYear);
            }
        }

        return $this->templateHelper->render('spots/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Recente spots',
            'maxMonths' => $maxMonths,
            'location' => $spotFilter->location,
            TemplateHelper::PARAMETER_DAY_NUMBER => $spotFilter->dayNumber,
            'spotDate' => !\is_null($spotFilter->spotDate) ? $spotFilter->spotDate->format('d-m-Y') : null,
            'trainNumber' => $spotFilter->trainNumber,
            'routeNumber' => $spotFilter->routeNumber,
            'spots' => $spots,
        ]);
    }
}
