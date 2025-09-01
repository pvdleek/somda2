<?php

declare(strict_types=1);

namespace App\Model;

use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

class SpotFilter
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Location abbreviation", type="string")
     */
    public ?string $location = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The day-number", enum={1,2,3,4,5,6,7}, type="integer")
     */
    public int $day_number = 0;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the spot (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $spot_date = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The train-number", type="string")
     */
    public ?string $train_number = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The route-number", type="string")
     */
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
