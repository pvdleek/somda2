<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_stats_blokken")
 * @ORM\Entity
 */
class StatisticBlock
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(name="blokid", referencedColumnName="blokid")
     * @ORM\Id
     */
    public ?Block $block = null;

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public ?\DateTime $date = null;

    /**
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    public int $views = 0;
}
