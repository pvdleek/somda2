<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helpers\TemplateHelper;
use App\Model\StatisticBusiest;
use App\Model\StatisticSummary;
use App\Repository\StatisticRepository;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController
{
    public function __construct(
        private readonly TemplateHelper $template_helper,
        private readonly StatisticRepository $statistic_repository,
    ) {
    }

    public function indexAction(): Response
    {
        $statistics_per_month = $this->statistic_repository->getTotalsPerMonth();
        $statistics_per_day = $this->statistic_repository->findLastDays(60);

        return $this->template_helper->render('somda/statistics.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Statistieken',
            'statistics_summary' => $this->getStatisticsSummary(),
            'statistics_per_month' => $statistics_per_month,
            'statistics_per_day' => $statistics_per_day,
        ]);
    }

    private function getStatisticsSummary(): StatisticSummary
    {
        $statistics_summary = new StatisticSummary();
        $statistics_summary->start_measurement = $this->statistic_repository->getFirstDate();
        $statistics_summary->pageviews_total = $this->statistic_repository->countPageViews();

        $busiest_pageviews = new StatisticBusiest(StatisticBusiest::TYPE_PAGE_VIEWS);
        $this->statistic_repository->findBusiest($busiest_pageviews);
        $statistics_summary->busiest_pageviews_date = $busiest_pageviews->timestamp;
        $statistics_summary->busiest_pageviews = $busiest_pageviews->number;

        $busiest_spots = new StatisticBusiest(StatisticBusiest::TYPE_SPOTS);
        $this->statistic_repository->findBusiest($busiest_spots);
        $statistics_summary->busiest_spots_date = $busiest_spots->timestamp;
        $statistics_summary->busiest_spots = $busiest_spots->number;

        $busiest_posts = new StatisticBusiest(StatisticBusiest::TYPE_POSTS);
        $this->statistic_repository->findBusiest($busiest_posts);
        $statistics_summary->busiest_posts_date = $busiest_posts->timestamp;
        $statistics_summary->busiest_posts = $busiest_posts->number;

        return $statistics_summary;
    }
}
