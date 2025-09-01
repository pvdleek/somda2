<?php

declare(strict_types=1);

namespace App\Model;

use App\Traits\DateTrait;

class Spot
{
    use DateTrait;

    public int $id;

    public \DateTime $spot_date;

    public ?int $spot_time;

    public string $route_number;

    public string $position_name;

    public string $train_number;

    public ?string $name_pattern_name;

    public ?string $extra;

    public int $spotter_id;

    public string $spotter_username;

    public string $location_name;

    public string $location_description;

    /**
     * @param array $queryResult - A result array from the findRecentWithSpotFilter function in the Spot repository
     */
    public function __construct(array $queryResult)
    {
        $this->id = (int) $queryResult['id'];
        $this->spot_date = $queryResult['spot_date'];
        $this->spot_time = isset($queryResult['spot_time']) ? $queryResult['spot_time'] : null;
        $this->route_number = $queryResult['route_number'];
        $this->position_name = $queryResult['position_name'];
        $this->train_number = $queryResult['train_number'];
        $this->name_pattern_name = $queryResult['name_pattern_name'];
        $this->extra = $queryResult['extra'];
        $this->spotter_id = (int) $queryResult['spotter_id'];
        $this->spotter_username = $queryResult['spotter_username'];
        $this->location_name = $queryResult['location_name'];
        $this->location_description = $queryResult['location_description'];
    }

    public function getDisplaySpotTime(): string
    {
        if (null !== $this->spot_time) {
            return $this->timeDatabaseToDisplay($this->spot_time);
        }
        return '';
    }
}
