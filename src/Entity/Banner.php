<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner")
 * @ORM\Entity
 */
class Banner extends Entity
{
    const LOCATION_HEADER = 'header';
    const LOCATION_FORUM = 'forum';

    /**
     * @var int
     * @ORM\Column(name="bannerid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string|null
     * @ORM\Column(name="code", type="string", length=6, nullable=true)
     */
    public $code;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default"=false})
     */
    public $active = false;

    /**
     * @var string
     * @ORM\Column(name="location", type="string", length=6, nullable=false, options={"default"="header"})
     */
    public $location = self::LOCATION_HEADER;

    /**
     * @var string|null
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    public $description;

    /**
     * @var string
     * @ORM\Column(name="link", type="string", length=100, nullable=false)
     */
    public $link = '';

    /**
     * @var string|null
     * @ORM\Column(name="image", type="string", length=100, nullable=true)
     */
    public $image;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    public $email = '';

    /**
     * @var int
     * @ORM\Column(name="max_views", type="bigint", nullable=true)
     */
    public $maxViews = 0;

    /**
     * @var int
     * @ORM\Column(name="max_hits", type="bigint", nullable=true)
     */
    public $maxHits = 0;

    /**
     * @var DateTime|null
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    public $startTimestamp;

    /**
     * @var DateTime|null
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    public $endTimestamp;

    /**
     * @var BannerHit[]
     * @ORM\OneToMany(targetEntity="App\Entity\BannerHit", mappedBy="banner")
     */
    private $bannerHits;

    /**
     * @var BannerView[]
     * @ORM\OneToMany(targetEntity="App\Entity\BannerView", mappedBy="banner")
     */
    private $bannerViews;

    /**
     * @var BannerCustomer
     * @ORM\ManyToOne(targetEntity="App\Entity\BannerCustomer", inversedBy="banners")
     * @ORM\JoinColumn(name="customerid", referencedColumnName="id")
     */
    public $customer;

    /**
     *
     */
    public function __construct()
    {
        $this->bannerHits = new ArrayCollection();
        $this->bannerViews = new ArrayCollection();
    }

    /**
     * @param BannerHit $bannerHit
     * @return Banner
     */
    public function addBannerHit(BannerHit $bannerHit): Banner
    {
        $this->bannerHits[] = $bannerHit;
        return $this;
    }

    /**
     * @return BannerHit[]
     */
    public function getBannerHits(): array
    {
        return $this->bannerHits->toArray();
    }

    /**
     * @param BannerView $bannerView
     * @return Banner
     */
    public function addBannerView(BannerView $bannerView): Banner
    {
        $this->bannerViews[] = $bannerView;
        return $this;
    }

    /**
     * @return BannerView[]
     */
    public function getBannerViews(): array
    {
        return $this->bannerViews->toArray();
    }
}
