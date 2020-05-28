<?php

namespace App\Twig;

use App\Helpers\DateHelper;
use App\Helpers\ForumHelper;
use App\Helpers\RouteOperationDaysHelper;
use App\Helpers\SortHelper;
use App\Helpers\SpotHelper;
use App\Helpers\UserDisplayHelper;
use App\Traits\DateTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    use DateTrait;

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('chr', 'chr'),
            new TwigFilter('displayUser', [UserDisplayHelper::class, 'getDisplayUser']),
            new TwigFilter('displayDateTime', [DateHelper::class, 'getDisplayDate']),
            new TwigFilter('displayTime', [$this, 'timeDatabaseToDisplay']),
            new TwigFilter('displayForumPost', [ForumHelper::class, 'getDisplayForumPost']),
            new TwigFilter('displaySpot', [SpotHelper::class, 'getDisplaySpot']),
            new TwigFilter('displayRouteOperationDays', [RouteOperationDaysHelper::class, 'getDisplay']),
            new TwigFilter('sortByField', [SortHelper::class, 'sortByFieldFilter']),
        ];
    }
}
