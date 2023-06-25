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
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="name", type="string", length=6, nullable=false)
     */
    public string $name = '';

    /**
     * @ORM\Column(name="max_views", type="integer", nullable=true)
     */
    public ?int $maxViews = null;

    /**
     * @ORM\Column(name="max_hits", type="integer", nullable=true)
     */
    public ?int $maxHits = null;

    /**
     * @ORM\Column(name="max_days", type="integer", nullable=true)
     */
    public ?int $maxDays = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Banner", mappedBy="customer")
     */
    private $banners;

    /**
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
