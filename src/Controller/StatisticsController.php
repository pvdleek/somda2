<?php

namespace App\Controller;

use App\Entity\Statistic;
use App\Helpers\TemplateHelper;
use App\Model\StatisticBusiest;
use App\Model\StatisticSummary;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly TemplateHelper $templateHelper,
    ) {
    }

    public function indexAction(): Response
    {
        $statisticsPerMonth = $this->doctrine->getRepository(Statistic::class)->getTotalsPerMonth();
        $statisticsPerDay = $this->doctrine->getRepository(Statistic::class)->findLastDays(60);

        return $this->templateHelper->render('somda/statistics.html.twig', [
            TemplateHelper::PARAMETER_PAGE_TITLE => 'Statistieken',
            'statisticsSummary' => $this->getStatisticsSummary(),
            'statisticsPerMonth' => $statisticsPerMonth,
            'statisticsPerDay' => $statisticsPerDay,
        ]);
    }

    private function getStatisticsSummary(): StatisticSummary
    {
        $statisticsSummary = new StatisticSummary();
        $statisticsSummary->startMeasurement = $this->doctrine->getRepository(Statistic::class)->getFirstDate();
        $statisticsSummary->pageViewsTotal = $this->doctrine->getRepository(Statistic::class)->countPageViews();

        $busiestPageViews = new StatisticBusiest(StatisticBusiest::TYPE_PAGE_VIEWS);
        $this->doctrine->getRepository(Statistic::class)->findBusiest($busiestPageViews);
        $statisticsSummary->busiestPageViewsDate = $busiestPageViews->timestamp;
        $statisticsSummary->busiestPageViews = $busiestPageViews->number;

        $busiestSpots = new StatisticBusiest(StatisticBusiest::TYPE_SPOTS);
        $this->doctrine->getRepository(Statistic::class)->findBusiest($busiestSpots);
        $statisticsSummary->busiestSpotsDate = $busiestSpots->timestamp;
        $statisticsSummary->busiestSpots = $busiestSpots->number;

        $busiestPosts = new StatisticBusiest(StatisticBusiest::TYPE_POSTS);
        $this->doctrine->getRepository(Statistic::class)->findBusiest($busiestPosts);
        $statisticsSummary->busiestPostsDate = $busiestPosts->timestamp;
        $statisticsSummary->busiestPosts = $busiestPosts->number;

        return $statisticsSummary;
    }
}
