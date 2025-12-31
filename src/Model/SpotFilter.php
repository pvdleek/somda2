<?php

declare(strict_types=1);

namespace App\Model;

class SpotFilter
{
    public ?string $location = null;

    public int $day_number = 0;

    public ?\DateTime $spot_date = null;

    public ?string $train_number = null;

    public ?string $route_number = null;

    public function createFromSearchParameters(array $parameters): void
    {
        $this->location = \strlen($parameters[0]) > 0 ? $parameters[0] : null;
        $this->day_number = (int) $parameters[1];
        try {
            $this->spot_date = \strlen($parameters[2]) > 0 ? \DateTime::createFromFormat('d-m-Y', $parameters[2]) : null;
        } catch (\Exception) {
            $this->spot_date = null;
        }
        $this->train_number = \strlen($parameters[3]) > 0 ? $parameters[3] : null;
        $this->route_number = \strlen($parameters[4]) > 0 ? $parameters[4] : null;
    }

    public function isValid(): bool
    {
        return null !== $this->location
            || $this->day_number > 0
            || null !== $this->spot_date
            || null !== $this->train_number
            || null !== $this->route_number;
    }
}
