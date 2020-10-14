<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bah_banner_hit")
 * @ORM\Entity
 */
class BannerHit
{
    /**
     * @var int|null
     * @ORM\Column(name="bah_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="bah_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var int
     * @ORM\Column(name="bah_ip_address", type="bigint", nullable=false)
     */
    public int $ipAddress;

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerHits")
     * @ORM\JoinColumn(name="bah_ban_id", referencedColumnName="ban_id")
     */
    public Banner $banner;
}
