<?php

namespace App\Model;

class StatisticSummary
{
    public ?\DateTime $startMeasurement = null;

    public int $pageViewsTotal = 0;

    public ?\DateTime $busiestPageViewsDate = null;

    public int $busiestPageViews = 0;

    public ?\DateTime $busiestSpotsDate = null;

    public int $busiestSpots = 0;

    public ?\DateTime $busiestPostsDate = null;

    public int $busiestPosts = 0;
}
