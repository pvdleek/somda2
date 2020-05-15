<?php

namespace App\Model;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class StatisticBusiest
{
    public const TYPE_PAGE_VIEWS = 0;
    public const TYPE_SPOTS = 1;
    public const TYPE_POSTS = 2;
    public const TYPE_VALUES = [self::TYPE_PAGE_VIEWS, self::TYPE_SPOTS, self::TYPE_POSTS];

    /**
     * @param int $type
     */
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    /**
     * @var int
     * @Assert\Choice(choices=StatisticBusiest::TYPE_VALUES)
     */
    public int $type;

    /**
     * @var DateTime
     */
    public DateTime $timestamp;

    /**
     * @var int
     */
    public int $number;
}
