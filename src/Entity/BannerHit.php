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
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="datumtijd", type="integer", nullable=false)
     */
    private $datetime;

    /**
     * @var string
     * @ORM\Column(name="ip", type="string", length=15, nullable=false)
     */
    private $ip = '';

    /**
     * @var Banner
     * @ORM\ManyToOne(targetEntity="App\Entity\Banner", inversedBy="bannerHits")
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
     * @return BannerHit
     */
    public function setId(int $id): BannerHit
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getDatetime(): int
    {
        return $this->datetime;
    }

    /**
     * @param int $datetime
     * @return BannerHit
     */
    public function setDatetime(int $datetime): BannerHit
    {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return BannerHit
     */
    public function setIp(string $ip): BannerHit
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return Banner
     */
    public function getBanner(): Banner
    {
        return $this->banner;
    }

    /**
     * @param Banner $banner
     * @return BannerHit
     */
    public function setBanner(Banner $banner): BannerHit
    {
        $this->banner = $banner;
        return $this;
    }
}
