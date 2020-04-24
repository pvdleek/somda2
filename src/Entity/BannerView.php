<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_views", indexes={@ORM\Index(name="idx_47831_bannerid", columns={"bannerid"})})
 * @ORM\Entity
 */
class BannerView extends Entity
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
     * @var int
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    public int $ipAddress;

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerViews")
     * @ORM\JoinColumn(name="bannerid", referencedColumnName="bannerid")
     */
    public Banner $banner;
}
