<?php

namespace App\Model;

use App\Entity\Location;
use DateTime;

class SpotInput
{
    /**
     * @var DateTime
     */
    public DateTime $spotDate;

    /**
     * @var string
     */
    public string $trainNumber;

    /**
     * @var string
     */
    public string $routeNumber;

    /**
     * @var Location|null
     */
    public ?Location $location;

    /**
     * @var int
     */
    public int $positionId;

    /**
     * @var string
     */
    public string $extra;
}
