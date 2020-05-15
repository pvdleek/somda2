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
    public DateTime $busiestPageViewsTimestamp;

    /**
     * @var int
     */
    public int $busiestPageViews;

    /**
     * @var DateTime
     */
    public DateTime $busiestSpotsTimestamp;

    /**
     * @var int
     */
    public int $busiestSpots;

    /**
     * @var DateTime
     */
    public DateTime $busiestPostsTimestamp;

    /**
     * @var int
     */
    public int $busiestPosts;
}
