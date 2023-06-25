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
    public int $dayNumber = 0;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the spot (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $spotDate = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The train-number", type="string")
     */
    public ?string $trainNumber = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The route-number", type="string")
     */
    public ?string $routeNumber = null;

    public function createFromSearchParameters(array $parameters): void
    {
        $this->location = \strlen($parameters[0]) > 0 ? $parameters[0] : null;
        $this->dayNumber = (int) $parameters[1];
        try {
            $this->spotDate = \strlen($parameters[2]) > 0 ? \DateTime::createFromFormat('d-m-Y', $parameters[2]) : null;
        } catch (\Exception $exception) {
            $this->spotDate = null;
        }
        $this->trainNumber = \strlen($parameters[3]) > 0 ? $parameters[3] : null;
        $this->routeNumber = \strlen($parameters[4]) > 0 ? $parameters[4] : null;
    }

    public function isValid(): bool
    {
        return !\is_null($this->location)
            || $this->dayNumber > 0
            || !\is_null($this->spotDate)
            || !\is_null($this->trainNumber)
            || !\is_null($this->routeNumber);
    }
}
