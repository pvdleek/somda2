<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr12
 *
 * @ORM\Table(name="somda_tdr_12", indexes={@ORM\Index(name="idx_48331_tijd", columns={"tijd"}), @ORM\Index(name="idx_48331_locatieid", columns={"locatieid"}), @ORM\Index(name="idx_48331_treinid", columns={"treinid"})})
 * @ORM\Entity
 */
class SomdaTdr12
{
    /**
     * @var int
     *
     * @ORM\Column(name="tdrid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tdrid;

    /**
     * @var int
     *
     * @ORM\Column(name="orderid", type="bigint", nullable=false)
     */
    private $orderid;

    /**
     * @var int
     *
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     */
    private $treinid;

    /**
     * @var int
     *
     * @ORM\Column(name="rijdagenid", type="bigint", nullable=false)
     */
    private $rijdagenid;

    /**
     * @var int
     *
     * @ORM\Column(name="locatieid", type="bigint", nullable=false)
     */
    private $locatieid;

    /**
     * @var string
     *
     * @ORM\Column(name="actie", type="string", length=1, nullable=false, options={"default"="-"})
     */
    private $actie = '-';

    /**
     * @var int
     *
     * @ORM\Column(name="tijd", type="bigint", nullable=false)
     */
    private $tijd;

    /**
     * @var string|null
     *
     * @ORM\Column(name="spoor", type="string", length=3, nullable=true)
     */
    private $spoor;


}
