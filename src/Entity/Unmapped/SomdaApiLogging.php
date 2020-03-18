<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaApiLogging
 *
 * @ORM\Table(name="somda_api_logging")
 * @ORM\Entity
 */
class SomdaApiLogging
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
     * @var string|null
     *
     * @ORM\Column(name="station", type="string", length=10, nullable=true)
     */
    private $station;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tijd", type="string", length=5, nullable=true)
     */
    private $tijd;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dagnr", type="bigint", nullable=true)
     */
    private $dagnr;

    /**
     * @var int|null
     *
     * @ORM\Column(name="resultaat_id", type="bigint", nullable=true)
     */
    private $resultaatId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="datumtijd", type="bigint", nullable=true)
     */
    private $datumtijd;


}
