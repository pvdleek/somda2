<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_customer")
 * @ORM\Entity
 */
class BannerCustomer extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=6, nullable=false)
     */
    public string $name;

    /**
     * @var int|null
     * @ORM\Column(name="max_views", type="bigint", nullable=true)
     */
    public ?int $maxViews;

    /**
     * @var int|null
     * @ORM\Column(name="max_hits", type="bigint", nullable=true)
     */
    public ?int $maxHits;

    /**
     * @var int|null
     * @ORM\Column(name="max_days", type="bigint", nullable=true)
     */
    public ?int $maxDays;

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
