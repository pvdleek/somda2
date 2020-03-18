<?php

namespace App\Entity;

use DateTime;
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
     * @var int
     * @ORM\Column(name="bannerid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(name="code", type="string", length=6, nullable=true)
     */
    private $code;

    /**
     * @var bool
     * @ORM\Column(name="active", type="bigint", nullable=false)
     */
    private $active = false;

    /**
     * @var string
     * @ORM\Column(name="location", type="string", length=6, nullable=false, options={"default"="header"})
     */
    private $location = self::LOCATION_HEADER;

    /**
     * @var string|null
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="link", type="string", length=100, nullable=false)
     */
    private $link = '';

    /**
     * @var string|null
     * @ORM\Column(name="image", type="string", length=100, nullable=true)
     */
    private $image;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email = '';

    /**
     * @var int
     * @ORM\Column(name="max_views", type="bigint", nullable=true)
     */
    private $maxViews = 0;

    /**
     * @var int
     * @ORM\Column(name="views", type="bigint", nullable=false)
     */
    private $views = 0;

    /**
     * @var int
     * @ORM\Column(name="max_hits", type="bigint", nullable=true)
     */
    private $maxHits = 0;

    /**
     * @var int
     * @ORM\Column(name="hits", type="bigint", nullable=false)
     */
    private $hits = 0;

    /**
     * @var DateTime|null
     * @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var DateTime|null
     * @ORM\Column(name="start_time", type="time", nullable=true)
     */
    private $startTime;

    /**
     * @var DateTime|null
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var DateTime|null
     * @ORM\Column(name="end_time", type="time", nullable=true)
     */
    private $endTime;

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
    private $customer;

    /**
     *
     */
    public function __construct()
    {
        $this->bannerHits = new ArrayCollection();
        $this->bannerViews = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Banner
     */
    public function setId(int $id): Banner
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return Banner
     */
    public function setCode(?string $code): Banner
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Banner
     */
    public function setActive(bool $active): Banner
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     * @return Banner
     */
    public function setLocation(string $location): Banner
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Banner
     */
    public function setDescription(?string $description): Banner
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return Banner
     */
    public function setLink(string $link): Banner
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return Banner
     */
    public function setImage(?string $image): Banner
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Banner
     */
    public function setEmail(string $email): Banner
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxViews(): int
    {
        return $this->maxViews;
    }

    /**
     * @param int $maxViews
     * @return Banner
     */
    public function setMaxViews(int $maxViews): Banner
    {
        $this->maxViews = $maxViews;
        return $this;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     * @return Banner
     */
    public function setViews(int $views): Banner
    {
        $this->views = $views;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxHits(): int
    {
        return $this->maxHits;
    }

    /**
     * @param int $maxHits
     * @return Banner
     */
    public function setMaxHits(int $maxHits): Banner
    {
        $this->maxHits = $maxHits;
        return $this;
    }

    /**
     * @return int
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * @param int $hits
     * @return Banner
     */
    public function setHits(int $hits): Banner
    {
        $this->hits = $hits;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $startDate
     * @return Banner
     */
    public function setStartDate(?DateTime $startDate): Banner
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    /**
     * @param DateTime|null $startTime
     * @return Banner
     */
    public function setStartTime(?DateTime $startTime): Banner
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     * @return Banner
     */
    public function setEndDate(?DateTime $endDate): Banner
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    /**
     * @param DateTime|null $endTime
     * @return Banner
     */
    public function setEndTime(?DateTime $endTime): Banner
    {
        $this->endTime = $endTime;
        return $this;
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

    /**
     * @return BannerCustomer
     */
    public function getCustomer(): BannerCustomer
    {
        return $this->customer;
    }

    /**
     * @param BannerCustomer $customer
     * @return Banner
     */
    public function setCustomer(BannerCustomer $customer): Banner
    {
        $this->customer = $customer;
        return $this;
    }
}
