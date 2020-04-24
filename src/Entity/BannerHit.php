<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_hits")
 * @ORM\Entity
 */
class BannerHit extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @var DateTime
     * @ORM\Column(name="datumtijd", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="ip", type="string", length=15, nullable=false)
     */
    public string $ipAddress = '';

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerHits")
     * @ORM\JoinColumn(name="bannerid", referencedColumnName="bannerid")
     */
    public Banner $banner;
}
