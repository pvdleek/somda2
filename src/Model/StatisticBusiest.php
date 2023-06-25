<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class StatisticBusiest
{
    public const TYPE_PAGE_VIEWS = 0;
    public const TYPE_SPOTS = 1;
    public const TYPE_POSTS = 2;
    public const TYPE_VALUES = [self::TYPE_PAGE_VIEWS, self::TYPE_SPOTS, self::TYPE_POSTS];

    /**
     * @Assert\Choice(choices=StatisticBusiest::TYPE_VALUES)
     */
    public ?int $type = null;

    public ?\DateTime $timestamp = null;

    public int $number = 0;

    public function __construct(int $type)
    {
        $this->type = $type;
    }
}
