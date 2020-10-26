<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ban_banner", indexes={@ORM\Index(name="IDX_ban_bac_id", columns={"ban_bac_id"})})
 * @ORM\Entity
 */
class Banner
{
    const LOCATION_HEADER = 'header';
    const LOCATION_FORUM = 'forum';

    /**
     * @var int|null
     * @ORM\Column(name="ban_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string|null
     * @ORM\Column(name="ban_code", type="string", length=6, nullable=true)
     */
    public ?string $code;

    /**
     * @var bool
     * @ORM\Column(name="ban_active", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $active = false;

    /**
     * @var string
     * @ORM\Column(name="ban_location", type="string", length=6, nullable=false, options={"default"="header"})
     */
    public string $location = self::LOCATION_HEADER;

    /**
     * @var string|null
     * @ORM\Column(name="ban_description", type="text", length=0, nullable=true)
     */
    public ?string $description;

    /**
     * @var string
     * @ORM\Column(name="ban_link", type="string", length=100, nullable=false)
     */
    public string $link = '';

    /**
     * @var string|null
     * @ORM\Column(name="ban_image", type="string", length=100, nullable=true)
     */
    public ?string $image;

    /**
     * @var string
     * @ORM\Column(name="ban_email", type="string", length=50, nullable=false)
     */
    public string $email = '';

    /**
     * @var int
     * @ORM\Column(name="ban_max_views", type="integer", nullable=false, options={"default"="0"})
     */
    public int $maxViews = 0;

    /**
     * @var int
     * @ORM\Column(name="ban_max_hits", type="integer", nullable=false, options={"default"="0"})
     */
    public int $maxHits = 0;

    /**
     * @var DateTime|null
     * @ORM\Column(name="ban_start_date", type="datetime", nullable=true)
     */
    public ?DateTime $startTimestamp;

    /**
     * @var DateTime|null
     * @ORM\Column(name="ban_end_date", type="datetime", nullable=true)
     */
    public ?DateTime $endTimestamp;

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
     * @ORM\JoinColumn(name="ban_bac_id", referencedColumnName="bac_id")
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
