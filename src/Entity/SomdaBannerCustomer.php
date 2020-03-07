<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaBannerCustomer
 *
 * @ORM\Table(name="somda_banner_customer")
 * @ORM\Entity
 */
class SomdaBannerCustomer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_views", type="bigint", nullable=true)
     */
    private $maxViews;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_hits", type="bigint", nullable=true)
     */
    private $maxHits;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_days", type="bigint", nullable=true)
     */
    private $maxDays;


}
