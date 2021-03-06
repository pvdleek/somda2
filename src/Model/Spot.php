<?php
declare(strict_types=1);

namespace App\Model;

use App\Traits\DateTrait;
use DateTime;

class Spot
{
    use DateTrait;

    /**
     * @var int
     */
    public int $id;

    /**
     * @var DateTime
     */
    public DateTime $spotDate;

    /**
     * @var int|null
     */
    public ?int $spotTime;

    /**
     * @var string
     */
    public string $routeNumber;

    /**
     * @var string
     */
    public string $positionName;

    /**
     * @var string
     */
    public string $trainNumber;

    /**
     * @var string|null
     */
    public ?string $namePatternName;

    /**
     * @var string|null
     */
    public ?string $extra;

    /**
     * @var int
     */
    public int $spotterId;

    /**
     * @var string
     */
    public string $spotterUsername;

    /**
     * @var string
     */
    public string $locationName;

    /**
     * @var string
     */
    public string $locationDescription;

    /**
     * @param array $queryResult - A result array from the findRecentWithSpotFilter function in the Spot repository
     */
    public function __construct(array $queryResult)
    {
        $this->id = (int)$queryResult['id'];
        $this->spotDate = $queryResult['spotDate'];
        $this->spotTime = isset($queryResult['spotTime']) ? $queryResult['spotTime'] : null;
        $this->routeNumber = $queryResult['routeNumber'];
        $this->positionName = $queryResult['positionName'];
        $this->trainNumber = $queryResult['trainNumber'];
        $this->namePatternName = $queryResult['namePatternName'];
        $this->extra = $queryResult['extra'];
        $this->spotterId = (int)$queryResult['spotterId'];
        $this->spotterUsername = $queryResult['spotterUsername'];
        $this->locationName = $queryResult['locationName'];
        $this->locationDescription = $queryResult['locationDescription'];
    }

    /**
     * @return string
     */
    public function getDisplaySpotTime(): string
    {
        if (!is_null($this->spotTime)) {
            return $this->timeDatabaseToDisplay($this->spotTime);
        }
        return '';
    }
}
