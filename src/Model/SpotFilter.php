<?php
declare(strict_types=1);

namespace App\Model;

use DateTime;
use Exception;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

class SpotFilter
{
    /**
     * @var string|null
     * @JMS\Expose()
     * @SWG\Property(description="Location abbreviation", type="string")
     */
    public ?string $location = null;

    /**
     * @var int
     * @JMS\Expose()
     * @SWG\Property(description="The day-number", enum={1,2,3,4,5,6,7}, type="integer")
     */
    public int $dayNumber = 0;

    /**
     * @var DateTime|null
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the spot (Y-m-dTH:i:sP)", type="string")
     */
    public ?DateTime $spotDate = null;

    /**
     * @var string|null
     * @JMS\Expose()
     * @SWG\Property(description="The train-number", type="string")
     */
    public ?string $trainNumber = null;

    /**
     * @var string|null
     * @JMS\Expose()
     * @SWG\Property(description="The route-number", type="string")
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
            $this->spotDate = strlen($parameters[2]) > 0 ? DateTime::createFromFormat('d-m-Y', $parameters[2]) : null;
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
