<?php

namespace App\Model;

use DateTime;

class StatisticSummary
{
    /**
     * @var DateTime
     */
    public DateTime $startMeasurement;

    /**
     * @var int
     */
    public int $pageViewsTotal;

    /**
     * @var DateTime
     */
    public DateTime $busiestPageViewsDate;

    /**
     * @var int
     */
    public int $busiestPageViews;

    /**
     * @var DateTime
     */
    public DateTime $busiestSpotsDate;

    /**
     * @var int
     */
    public int $busiestSpots;

    /**
     * @var DateTime
     */
    public DateTime $busiestPostsDate;

    /**
     * @var int
     */
    public int $busiestPosts;
}
