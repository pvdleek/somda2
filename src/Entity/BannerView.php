<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_views", indexes={@ORM\Index(name="idx_47831_bannerid", columns={"bannerid"})})
 * @ORM\Entity
 */
class BannerView
{
    /**
     * @var int|null
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    public string $ipAddress;

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerViews")
     * @ORM\JoinColumn(name="bannerid", referencedColumnName="bannerid")
     */
    public Banner $banner;
}
