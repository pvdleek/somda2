<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_customer_user")
 * @ORM\Entity
 */
class BannerCustomerUser
{
    /**
     * @var bool
     * @ORM\Column(name="allowed_new", type="boolean", nullable=false)
     */
    private $allowedNew = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_max_views", type="boolean", nullable=false)
     */
    private $allowedMaxViews = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_max_hits", type="boolean", nullable=false)
     */
    private $allowedMaxHits = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_max_date", type="boolean", nullable=false)
     */
    private $allowedMaxDate = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_deactivate", type="boolean", nullable=false)
     */
    private $allowedDeactivate = false;

    /**
     * @var BannerCustomer
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\BannerCustomer", inversedBy="customerUsers")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var User
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    private $user;

    /**
     * @param bool $allowedNew
     * @return BannerCustomerUser
     */
    public function setAllowedNew(bool $allowedNew) : BannerCustomerUser
    {
        $this->allowedNew = $allowedNew;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowedNew() : bool
    {
        return $this->allowedNew;
    }

    /**
     * @param bool $allowedMaxViews
     * @return BannerCustomerUser
     */
    public function setAllowedMaxViews(bool $allowedMaxViews) : BannerCustomerUser
    {
        $this->allowedMaxViews = $allowedMaxViews;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowedMaxViews() : bool
    {
        return $this->allowedMaxViews;
    }

    /**
     * @param bool $allowedMaxHits
     * @return BannerCustomerUser
     */
    public function setAllowedMaxHits(bool $allowedMaxHits) : BannerCustomerUser
    {
        $this->allowedMaxHits = $allowedMaxHits;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowedMaxHits() : bool
    {
        return $this->allowedMaxHits;
    }

    /**
     * @param bool $allowedMaxDate
     * @return BannerCustomerUser
     */
    public function setAllowedMaxDate(bool $allowedMaxDate) : BannerCustomerUser
    {
        $this->allowedMaxDate = $allowedMaxDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowedMaxDate() : BannerCustomerUser
    {
        return $this->allowedMaxDate;
    }

    /**
     * @param bool $allowedDeactivate
     * @return BannerCustomerUser
     */
    public function setAllowedDeactivate(bool $allowedDeactivate) : BannerCustomerUser
    {
        $this->allowedDeactivate = $allowedDeactivate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowedDeactivate() : bool
    {
        return $this->allowedDeactivate;
    }

    /**
     * @param BannerCustomer $customer
     * @return BannerCustomerUser
     */
    public function setCustomer(BannerCustomer $customer = null) : BannerCustomerUser
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return BannerCustomer
     */
    public function getCustomer() : BannerCustomer
    {
        return $this->customer;
    }

    /**
     * @param User $user
     * @return BannerCustomerUser
     */
    public function setUser(User $user = null) : BannerCustomerUser
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser() : User
    {
        return $this->user;
    }
}
