<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaStatsBlokken
 *
 * @ORM\Table(name="somda_stats_blokken")
 * @ORM\Entity
 */
class SomdaStatsBlokken
{
    /**
     * @var int
     *
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $blokid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    private $pageviews = '0';


}
