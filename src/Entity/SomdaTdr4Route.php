<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr4Route
 *
 * @ORM\Table(name="somda_tdr_4_route")
 * @ORM\Entity
 */
class SomdaTdr4Route
{
    /**
     * @var int
     *
     * @ORM\Column(name="treinnummerlijst_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $treinnummerlijstId;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $volgorde;

    /**
     * @var int
     *
     * @ORM\Column(name="locatieid", type="bigint", nullable=false)
     */
    private $locatieid;


}
