<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_banner_customer')]
class BannerCustomer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(length: 6, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(nullable: true, options: ['unsigned' => true])]
    public ?int $max_views = null;

    #[ORM\Column(nullable: true, options: ['unsigned' => true])]
    public ?int $max_hits = null;

    #[ORM\Column(nullable: true, options: ['unsigned' => true])]
    public ?int $max_days = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: 'App\Entity\Banner')]
    private Collection $banners;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: 'App\Entity\BannerCustomerUser')]
    private Collection $customer_users;

    public function __construct()
    {
        $this->banners = new ArrayCollection();
        $this->customer_users = new ArrayCollection();
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

    public function addCustomerUser(BannerCustomerUser $customer_user): BannerCustomer
    {
        $this->customer_users[] = $customer_user;
        
        return $this;
    }

    /**
     * @return BannerCustomerUser[]
     */
    public function getCustomerUsers(): array
    {
        return $this->customer_users->toArray();
    }
}
