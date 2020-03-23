<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_views", indexes={@ORM\Index(name="idx_47831_bannerid", columns={"bannerid"})})
 * @ORM\Entity
 */
class BannerView
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="datumtijd", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var int
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    private $ip;

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerViews")
     * @ORM\JoinColumn(name="bannerid", referencedColumnName="bannerid")
     */
    private $banner;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return BannerView
     */
    public function setId(int $id): BannerView
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return BannerView
     */
    public function setTimestamp(int $timestamp): BannerView
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getIp(): int
    {
        return $this->ip;
    }

    /**
     * @param int $ip
     * @return BannerView
     */
    public function setIp(int $ip): BannerView
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @param mixed $banner
     * @return BannerView
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;
        return $this;
    }
}
