<?php

namespace App\Twig;

use App\Helpers\DateHelper;
use App\Helpers\ForumHelper;
use App\Helpers\SortHelper;
use App\Helpers\UserHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('chr', 'chr'),
            new TwigFilter('displayUser', [UserHelper::class, 'getDisplayUser']),
            new TwigFilter('displayDateTime', [DateHelper::class, 'getDisplayDate']),
            new TwigFilter('displayTime', [DateHelper::class, 'timeDatabaseToDisplay']),
            new TwigFilter('displayForumPost', [ForumHelper::class, 'getDisplayForumPost']),
            new TwigFilter('sortByField', [SortHelper::class, 'sortByFieldFilter']),
        ];
    }
}
