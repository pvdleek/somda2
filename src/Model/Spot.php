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
     * @param array $query_result - A result array from the findRecentWithSpotFilter function in the Spot repository
     */
    public function __construct(array $query_result)
    {
        $this->id = (int) $query_result['id'];
        $this->spot_date = $query_result['spot_date'];
        $this->spot_time = isset($query_result['spot_time']) ? $query_result['spot_time'] : null;
        $this->route_number = $query_result['route_number'];
        $this->position_name = $query_result['position_name'];
        $this->train_number = $query_result['train_number'];
        $this->name_pattern_name = $query_result['name_pattern_name'];
        $this->extra = $query_result['extra'];
        $this->spotter_id = (int) $query_result['spotter_id'];
        $this->spotter_username = $query_result['spotter_username'];
        $this->location_name = $query_result['location_name'];
        $this->location_description = $query_result['location_description'];
    }

    public function getDisplaySpotTime(): string
    {
        if (null !== $this->spot_time) {
            return $this->timeDatabaseToDisplay($this->spot_time);
        }
        return '';
    }
}
