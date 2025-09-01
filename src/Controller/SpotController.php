<?php

declare(strict_types=1);

namespace App\Controller;

use App\Generics\RoleGenerics;
use App\Helpers\FlashHelper;
use App\Helpers\RedirectHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Model\SpotFilter;
use App\Repository\SpotRepository;
use App\Repository\TrainTableYearRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SpotController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserHelper $user_helper,
        private readonly TemplateHelper $template_helper,
        private readonly RedirectHelper $redirect_helper,
        private readonly FlashHelper $flash_helper,
        private readonly SpotRepository $spot_repository,
        private readonly TrainTableYearRepository $train_table_year_repository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function redirectToTrainTableAction(string $route_number, string $date): RedirectResponse
    {
        $checkDate = new \DateTime($date);
        $train_table_year = $this->train_table_year_repository->findTrainTableYearByDate($checkDate);

        return $this->redirect_helper->redirectToRoute(
            'train_table_search',
            ['train_table_year_id' => $train_table_year->id, 'route_number' => $route_number]
        );
    }

    public function indexAction(int $max_months = 1, ?string $search_parameters = null): Response
    {
        $this->user_helper->denyAccessUnlessGranted(RoleGenerics::ROLE_SPOTS_RECENT);

        $train_table_year = $this->train_table_year_repository->findTrainTableYearByDate(new \DateTime());

        $spot_filter = new SpotFilter();
        $spots = null;

        if (null !== $search_parameters) {
            $spot_filter->createFromSearchParameters(explode('/', $search_parameters));

            if (!$spot_filter->isValid()) {
                $this->flash_helper->add(
                    FlashHelper::FLASH_TYPE_WARNING,
                    'Het is niet mogelijk om spots te bekijken zonder filter, kies minimaal 1 filter'
                );
            } else {
                $spots = $this->spot_repository->findRecentWithSpotFilter($max_months, $spot_filter, $train_table_year);
            }
        }

        return $this->template_helper->render('spots/index.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Recente spots',
            'max_months' => $max_months,
            'location' => $spot_filter->location,
            TemplateHelper::PARAMETER_DAY_NUMBER => $spot_filter->day_number,
            'spot_date' => null !== $spot_filter->spot_date ? $spot_filter->spot_date->format('d-m-Y') : null,
            'train_number' => $spot_filter->train_number,
            'route_number' => $spot_filter->route_number,
            'spots' => $spots,
        ]);
    }
}
