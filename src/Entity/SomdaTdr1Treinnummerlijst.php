<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr1Treinnummerlijst
 *
 * @ORM\Table(name="somda_tdr_1_treinnummerlijst", indexes={@ORM\Index(name="idx_48442_nr_start", columns={"nr_start"}), @ORM\Index(name="idx_48442_nr_eind", columns={"nr_eind"})})
 * @ORM\Entity
 */
class SomdaTdr1Treinnummerlijst
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
     * @var int
     *
     * @ORM\Column(name="nr_start", type="bigint", nullable=false)
     */
    private $nrStart;

    /**
     * @var int
     *
     * @ORM\Column(name="nr_eind", type="bigint", nullable=false)
     */
    private $nrEind;

    /**
     * @var int|null
     *
     * @ORM\Column(name="vervoerder_id", type="bigint", nullable=true)
     */
    private $vervoerderId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="karakteristiek_id", type="bigint", nullable=true)
     */
    private $karakteristiekId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="traject", type="string", length=75, nullable=true)
     */
    private $traject;


}
