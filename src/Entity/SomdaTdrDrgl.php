<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdrDrgl
 *
 * @ORM\Table(name="somda_tdr_drgl")
 * @ORM\Entity
 */
class SomdaTdrDrgl
{
    /**
     * @var int
     *
     * @ORM\Column(name="tdr_nr", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tdrNr;

    /**
     * @var string
     *
     * @ORM\Column(name="naam", type="string", length=10, nullable=false)
     */
    private $naam;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_datum", type="date", nullable=false)
     */
    private $startDatum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eind_datum", type="date", nullable=false)
     */
    private $eindDatum;


}
