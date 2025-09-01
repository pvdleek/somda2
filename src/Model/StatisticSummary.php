<?php

declare(strict_types=1);

namespace App\Model;

class StatisticSummary
{
    public ?\DateTime $start_measurement = null;

    public int $pageviews_total = 0;

    public ?\DateTime $busiest_pageviews_date = null;

    public int $busiest_pageviews = 0;

    public ?\DateTime $busiest_spots_date = null;

    public int $busiest_spots = 0;

    public ?\DateTime $busiest_posts_date = null;

    public int $busiest_posts = 0;
}
