<?php

declare(strict_types=1);

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
    public int $order = 1;

    public Route $route;

    public Transporter $transporter;

    public int $first_stop_number;

    public int $last_stop_number;
}
