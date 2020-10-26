<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bcu_banner_customer_user", indexes={
 *     @ORM\Index(name="IDX_bcu_bac_id", columns={"bcu_bac_id"}),
 *     @ORM\Index(name="IDX_bcu_use_id", columns={"bcu_use_id"})
 * })
 * @ORM\Entity
 */
class BannerCustomerUser
{
    /**
     * @var bool
     * @ORM\Column(name="bcu_allowed_new", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedNew = false;

    /**
     * @var bool
     * @ORM\Column(name="bcu_allowed_max_views", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedMaxViews = false;

    /**
     * @var bool
     * @ORM\Column(name="bcu_allowed_max_hits", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedMaxHits = false;

    /**
     * @var bool
     * @ORM\Column(name="bcu_allowed_max_date", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedMaxDate = false;

    /**
     * @var bool
     * @ORM\Column(name="bcu_allowed_deactivate", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedDeactivate = false;

    /**
     * @var BannerCustomer
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\BannerCustomer", inversedBy="customerUsers")
     * @ORM\JoinColumn(name="bcu_bac_id", referencedColumnName="bac_id")
     */
    public BannerCustomer $customer;

    /**
     * @var User
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="bcu_use_id", referencedColumnName="use_id")
     */
    public User $user;
}
