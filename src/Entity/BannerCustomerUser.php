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
     * @ORM\Column(name="allowed_new", type="boolean", nullable=false, options={"default"=false})
     */
    public $allowedNew = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_max_views", type="boolean", nullable=false, options={"default"=false})
     */
    public $allowedMaxViews = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_max_hits", type="boolean", nullable=false, options={"default"=false})
     */
    public $allowedMaxHits = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_max_date", type="boolean", nullable=false, options={"default"=false})
     */
    public $allowedMaxDate = false;

    /**
     * @var bool
     * @ORM\Column(name="allowed_deactivate", type="boolean", nullable=false, options={"default"=false})
     */
    public $allowedDeactivate = false;

    /**
     * @var BannerCustomer
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\BannerCustomer", inversedBy="customerUsers")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    public $customer;

    /**
     * @var User
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    public $user;
}
