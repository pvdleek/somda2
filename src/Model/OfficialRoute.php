<?php

namespace App\Model;

use App\Entity\Route;
use App\Entity\Transporter;

/**
 * Class OfficialRoute
 * @package App\Model
 * Used in the OfficialTrainTableHelper as a model for the routes
 */
class OfficialRoute
{
    /**
     * @var int
     */
    public int $order = 1;

    /**
     * @var Route
     */
    public Route $route;

    /**
     * @var Transporter
     */
    public Transporter $transporter;

    /**
     * @var int
     */
    public int $firstStopNumber;

    /**
     * @var int
     */
    public int $lastStopNumber;
}
