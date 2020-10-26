<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="stb_statistic_block", indexes={@ORM\Index(name="IDX_stb_blo_id", columns={"stb_blo_id"})})
 * @ORM\Entity
 */
class StatisticBlock
{
    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(name="stb_blo_id", referencedColumnName="blo_id")
     * @ORM\Id
     */
    public Block $block;

    /**
     * @var DateTime
     * @ORM\Column(name="stb_date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public DateTime $date;

    /**
     * @var string
     * @ORM\Column(name="stb_views", type="bigint", nullable=false)
     */
    public string $views = '0';
}
