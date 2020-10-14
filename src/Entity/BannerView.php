<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bav_banner_view", indexes={@ORM\Index(name="idx_47831_bannerid", columns={"bav_ban_id"})})
 * @ORM\Entity
 */
class BannerView
{
    /**
     * @var int|null
     * @ORM\Column(name="bav_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="bav_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var int
     * @ORM\Column(name="bav_ip_address", type="bigint", nullable=false)
     */
    public int $ipAddress;

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerViews")
     * @ORM\JoinColumn(name="bav_ban_id", referencedColumnName="ban_id")
     */
    public Banner $banner;
}
