<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr9Route
 *
 * @ORM\Table(name="somda_tdr_9_route")
 * @ORM\Entity
 */
class SomdaTdr9Route
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
