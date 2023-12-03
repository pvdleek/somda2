<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_hits")
 * @ORM\Entity
 */
class BannerHit
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\Column(name="ip_address", type="bigint", nullable=false)
     */
    public int $ipAddress = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerHits")
     * @ORM\JoinColumn(name="bannerid", referencedColumnName="bannerid")
     */
    public ?Banner $banner = null;
}
