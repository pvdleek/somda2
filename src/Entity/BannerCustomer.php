<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_customer")
 * @ORM\Entity
 */
class BannerCustomer
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(name="name", type="string", length=6, nullable=true)
     */
    private $name;

    /**
     * @var int|null
     * @ORM\Column(name="max_views", type="bigint", nullable=true)
     */
    private $maxViews;

    /**
     * @var int|null
     * @ORM\Column(name="max_hits", type="bigint", nullable=true)
     */
    private $maxHits;

    /**
     * @var int|null
     * @ORM\Column(name="max_days", type="bigint", nullable=true)
     */
    private $maxDays;

    /**
     * @var Banner[]
     * @ORM\OneToMany(targetEntity="App\Entity\Banner", mappedBy="customer")
     */
    private $banners;

    /**
     * @var BannerCustomerUser[]
     * @ORM\OneToMany(targetEntity="App\Entity\BannerCustomerUser", mappedBy="customer")
     */
    private $customerUsers;

    /**
     *
     */
    public function __construct()
    {
        $this->banners = new ArrayCollection();
        $this->customerUsers = new ArrayCollection();
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
     * @return BannerCustomer
     */
    public function setId(int $id): BannerCustomer
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return BannerCustomer
     */
    public function setName(?string $name): BannerCustomer
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxViews(): ?int
    {
        return $this->maxViews;
    }

    /**
     * @param int|null $maxViews
     * @return BannerCustomer
     */
    public function setMaxViews(?int $maxViews): BannerCustomer
    {
        $this->maxViews = $maxViews;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxHits(): ?int
    {
        return $this->maxHits;
    }

    /**
     * @param int|null $maxHits
     * @return BannerCustomer
     */
    public function setMaxHits(?int $maxHits): BannerCustomer
    {
        $this->maxHits = $maxHits;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxDays(): ?int
    {
        return $this->maxDays;
    }

    /**
     * @param int|null $maxDays
     * @return BannerCustomer
     */
    public function setMaxDays(?int $maxDays): BannerCustomer
    {
        $this->maxDays = $maxDays;
        return $this;
    }

    /**
     * @param Banner $banner
     * @return BannerCustomer
     */
    public function addBanner(Banner $banner): BannerCustomer
    {
        $this->banners[] = $banner;
        return $this;
    }

    /**
     * @return Banner[]
     */
    public function getBanners(): array
    {
        return $this->banners->toArray();
    }

    /**
     * @param BannerCustomerUser $customerUser
     * @return BannerCustomer
     */
    public function addCustomerUser(BannerCustomerUser $customerUser): BannerCustomer
    {
        $this->customerUsers[] = $customerUser;
        return $this;
    }

    /**
     * @return BannerCustomerUser[]
     */
    public function getCustomerUsers(): array
    {
        return $this->customerUsers->toArray();
    }
}
