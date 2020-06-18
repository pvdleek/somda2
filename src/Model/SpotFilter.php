<?php

namespace App\Model;

use DateTime;
use Exception;

class SpotFilter
{
    /**
     * @var string|null
     */
    public ?string $location = null;

    /**
     * @var int
     */
    public int $dayNumber = 0;

    /**
     * @var DateTime|null
     */
    public ?DateTime $spotDate = null;

    /**
     * @var string|null
     */
    public ?string $trainNumber = null;

    /**
     * @var string|null
     */
    public ?string $routeNumber = null;

    /**
     * @param array $parameters
     */
    public function createFromSearchParameters(array $parameters): void
    {
        $this->location = strlen($parameters[0]) > 0 ? $parameters[0] : null;
        $this->dayNumber = (int)$parameters[1];
        try {
            $this->spotDate = strlen($parameters[2] > 0) ? DateTime::createFromFormat('d-m-Y', $parameters[2]) : null;
        } catch (Exception $exception) {
            $this->spotDate = null;
        }
        $this->trainNumber = strlen($parameters[3]) > 0 ? $parameters[3] : null;
        $this->routeNumber = strlen($parameters[4]) > 0 ? $parameters[4] : null;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return !is_null($this->location)
            || $this->dayNumber > 0
            || !is_null($this->spotDate)
            || !is_null($this->trainNumber)
            || !is_null($this->routeNumber);
    }
}
