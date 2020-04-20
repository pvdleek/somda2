<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_stats_blokken")
 * @ORM\Entity
 */
class StatisticBlock
{
    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(name="blokid", referencedColumnName="blokid")
     * @ORM\Id
     */
    public $block;

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public $date;

    /**
     * @var int
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    public $views = 0;
}
