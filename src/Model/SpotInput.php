<?php
declare(strict_types=1);

namespace App\Model;

use App\Entity\Location;
use App\Entity\User;

class SpotInput
{
    public ?int $existingSpotId = null;

    public User $user;

    public \DateTime $spot_date;

    public string $train_number;

    public string $route_number;

    public ?Location $location;

    public int $position_id;

    public string $extra;

    public ?string $user_extra = null;

    public int $feedback_flag = 0;
}
