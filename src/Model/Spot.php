<?php
declare(strict_types=1);

namespace App\Model;

use App\Traits\DateTrait;

class Spot
{
    use DateTrait;

    public int $id;

    public \DateTime $spotDate;

    public ?int $spotTime;

    public string $routeNumber;

    public string $positionName;

    public string $trainNumber;

    public ?string $namePatternName;

    public ?string $extra;

    public int $spotterId;

    public string $spotterUsername;

    public string $locationName;

    public string $locationDescription;

    /**
     * @param array $queryResult - A result array from the findRecentWithSpotFilter function in the Spot repository
     */
    public function __construct(array $queryResult)
    {
        $this->id = (int) $queryResult['id'];
        $this->spotDate = $queryResult['spotDate'];
        $this->spotTime = isset($queryResult['spotTime']) ? $queryResult['spotTime'] : null;
        $this->routeNumber = $queryResult['routeNumber'];
        $this->positionName = $queryResult['positionName'];
        $this->trainNumber = $queryResult['trainNumber'];
        $this->namePatternName = $queryResult['namePatternName'];
        $this->extra = $queryResult['extra'];
        $this->spotterId = (int) $queryResult['spotterId'];
        $this->spotterUsername = $queryResult['spotterUsername'];
        $this->locationName = $queryResult['locationName'];
        $this->locationDescription = $queryResult['locationDescription'];
    }

    public function getDisplaySpotTime(): string
    {
        if (null !== $this->spotTime) {
            return $this->timeDatabaseToDisplay($this->spotTime);
        }
        return '';
    }
}
