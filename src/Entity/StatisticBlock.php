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
    private $block;

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $date;

    /**
     * @var int
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    private $views = 0;

    /**
     * @return Block
     */
    public function getBlock(): Block
    {
        return $this->block;
    }

    /**
     * @param Block $block
     * @return StatisticBlock
     */
    public function setBlock(Block $block): StatisticBlock
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return StatisticBlock
     */
    public function setDate(DateTime $date): StatisticBlock
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     * @return StatisticBlock
     */
    public function setViews(int $views): StatisticBlock
    {
        $this->views = $views;
        return $this;
    }
}
