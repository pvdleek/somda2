<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_banner_customer_user")
 * @ORM\Entity
 */
class BannerCustomerUser
{
    /**
     * @ORM\Column(name="allowed_new", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedNew = false;

    /**
     * @ORM\Column(name="allowed_max_views", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedMaxViews = false;

    /**
     * @ORM\Column(name="allowed_max_hits", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedMaxHits = false;

    /**
     * @ORM\Column(name="allowed_max_date", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedMaxDate = false;

    /**
     * @ORM\Column(name="allowed_deactivate", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $allowedDeactivate = false;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\BannerCustomer", inversedBy="customerUsers")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    public ?BannerCustomer $customer = null;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    public ?User $user = null;
}
