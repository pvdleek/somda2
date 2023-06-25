<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner")
 * @ORM\Entity
 */
class Banner
{
    const LOCATION_HEADER = 'header';
    const LOCATION_FORUM = 'forum';

    /**
     * @ORM\Column(name="bannerid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="code", type="string", length=6, nullable=true)
     */
    public ?string $code = null;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $active = false;

    /**
     * @ORM\Column(name="location", type="string", length=6, nullable=false, options={"default"="header"})
     */
    public string $location = self::LOCATION_HEADER;

    /**
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    public ?string $description = null;

    /**
     * @ORM\Column(name="link", type="string", length=100, nullable=false)
     */
    public string $link = '';

    /**
     * @ORM\Column(name="image", type="string", length=100, nullable=true)
     */
    public ?string $image = null;

    /**
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    public string $email = '';

    /**
     * @ORM\Column(name="max_views", type="integer", nullable=false, options={"default"="0"})
     */
    public int $maxViews = 0;

    /**
     * @ORM\Column(name="max_hits", type="integer", nullable=false, options={"default"="0"})
     */
    public int $maxHits = 0;

    /**
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    public ?\DateTime $startTimestamp;

    /**
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    public ?\DateTime $endTimestamp;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BannerHit", mappedBy="banner")
     */
    private $bannerHits;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BannerView", mappedBy="banner")
     */
    private $bannerViews;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BannerCustomer", inversedBy="banners")
     * @ORM\JoinColumn(name="customerid", referencedColumnName="id")
     */
    public BannerCustomer $customer;

    /**
     *
     */
    public function __construct()
    {
        $this->bannerHits = new ArrayCollection();
        $this->bannerViews = new ArrayCollection();
    }

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
