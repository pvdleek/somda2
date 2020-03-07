<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaBannerCustomerUser
 *
 * @ORM\Table(name="somda_banner_customer_user")
 * @ORM\Entity
 */
class SomdaBannerCustomerUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="allowed_new", type="bigint", nullable=false)
     */
    private $allowedNew = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="allowed_max_views", type="bigint", nullable=false)
     */
    private $allowedMaxViews = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="allowed_max_hits", type="bigint", nullable=false)
     */
    private $allowedMaxHits = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="allowed_max_date", type="bigint", nullable=false)
     */
    private $allowedMaxDate = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="allowed_deactivate", type="bigint", nullable=false)
     */
    private $allowedDeactivate = '0';


}
