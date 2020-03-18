<?php

namespace App\Twig;

use App\Entity\Banner;
use App\Entity\RailNews;
use App\Helpers\BreadcrumbHelper;
use App\Helpers\DateHelper;
use App\Helpers\MenuHelper;
use App\Helpers\SortHelper;
use App\Helpers\UserHelper;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var MenuHelper
     */
    private $menuHelper;

    /**
     * @var BreadcrumbHelper
     */
    private $breadcrumbHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param MenuHelper $menuHelper
     * @param BreadcrumbHelper $breadcrumbHelper
     */
    public function __construct(ManagerRegistry $doctrine, MenuHelper $menuHelper, BreadcrumbHelper $breadcrumbHelper)
    {
        $this->doctrine = $doctrine;
        $this->menuHelper = $menuHelper;
        $this->breadcrumbHelper = $breadcrumbHelper;
    }

    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('chr', 'chr'),
            new TwigFilter('displayUser', [UserHelper::class, 'getDisplayUser']),
            new TwigFilter('displayDateTime', [DateHelper::class, 'getDisplayDate']),
            new TwigFilter('displayTime', [DateHelper::class, 'timeDatabaseToDisplay']),
            new TwigFilter('sortByField', [SortHelper::class, 'sortByFieldFilter']),
        ];
    }

    /**
     * @return array
     */
    public function getGlobals() : array
    {
        $globals = ['banner' => null, 'newsItem' => null];

        // Check for active banners in the header
        $banners = $this->doctrine->getRepository(Banner::class)->findBy(
            ['location' => Banner::LOCATION_HEADER, 'active' => 1]
        );
        if (count($banners) > 0) {
            $globals['banner'] = $banners[rand(0, count($banners) - 1)];
            // Give this banner a view
            $globals['banner']->setViews($globals['banner']->getViews() + 1);
            $this->doctrine->getManager()->flush();
        } else {
            // Get a recent news item
            $newsItems = $this->doctrine->getRepository(RailNews::class)->findBy(
                ['active' => true, 'approved' => true], ['dateTime' => 'DESC'], 3, rand(0, 2)
            );
            $globals['newsItem'] = $newsItems[0];
        }

        $globals['imageNr'] = rand(1, 11);
        $globals['nrOfOpenForumAlerts'] = $this->menuHelper->getNumberOfOpenForumAlerts();
        $globals['menuStructure'] = $this->menuHelper->getMenuStructure();
        $globals['breadcrumb'] = $this->breadcrumbHelper->getBreadcrumb();

        return $globals;
    }
}
