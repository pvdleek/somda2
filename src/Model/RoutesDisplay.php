<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\TrainTableYear;

class RoutesDisplay
{
    public ?TrainTableYear $train_table_year = null;

    /** @var RouteList[] */
    public array $route_lists = [];

    public ?RouteList $selected_route_list = null;

    /** @var Route[] */
    public array $routes = [];
}
