<?php

namespace App\Model;

use App\Entity\Route;
use App\Entity\RouteList;
use App\Entity\TrainTableYear;

class RoutesDisplay
{
    /**
     * @var TrainTableYear|null
     */
    public ?TrainTableYear $trainTableYear = null;

    /**
     * @var RouteList[]
     */
    public array $routeLists = [];

    /**
     * @var RouteList|null
     */
    public ?RouteList $selectedRouteList = null;

    /**
     * @var Route[]
     */
    public array $routes = [];
}
