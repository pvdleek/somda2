<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaMatNaam
 *
 * @ORM\Table(name="somda_mat_naam")
 * @ORM\Entity
 */
class SomdaMatNaam
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
     * @var int
     *
     * @ORM\Column(name="vervoerder_id", type="bigint", nullable=false)
     */
    private $vervoerderId;

    /**
     * @var string
     *
     * @ORM\Column(name="naam", type="string", length=25, nullable=false)
     */
    private $naam;


}
