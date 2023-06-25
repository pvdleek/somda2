<?php

namespace App\Model;

use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\TrainTableYear;

class RoutesDisplay
{
    public ?TrainTableYear $trainTableYear = null;

    /**
     * @var RouteList[]
     */
    public array $routeLists = [];

    public ?RouteList $selectedRouteList = null;

    /**
     * @var Route[]
     */
    public array $routes = [];
}
