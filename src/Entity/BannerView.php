<?php

namespace App\Entity;

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
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="datumtijd", type="bigint", nullable=false)
     */
    public $timestamp;

    /**
     * @var int
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    public $ipAddress;

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerViews")
     * @ORM\JoinColumn(name="bannerid", referencedColumnName="bannerid")
     */
    public $banner;
}
