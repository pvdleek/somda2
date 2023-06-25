<?php
declare(strict_types=1);

namespace App\Model;

use App\Entity\Location;
use App\Entity\User;

class SpotInput
{
    public ?int $existingSpotId = null;

    public User $user;

    public \DateTime $spotDate;

    public string $trainNumber;

    public string $routeNumber;

    public ?Location $location;

    public int $positionId;

    public string $extra;

    public ?string $userExtra = null;

    public int $feedbackFlag = 0;
}
