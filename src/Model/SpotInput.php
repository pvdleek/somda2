<?php
declare(strict_types=1);

namespace App\Model;

use App\Entity\Location;
use App\Entity\User;
use DateTime;

class SpotInput
{
    /**
     * @var int|null
     */
    public ?int $existingSpotId = null;

    /**
     * @var User
     */
    public User $user;

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

    /**
     * @var string|null
     */
    public ?string $userExtra = null;

    /**
     * @var int
     */
    public int $feedbackFlag = 0;
}
