<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaLogging
 *
 * @ORM\Table(name="somda_logging")
 * @ORM\Entity
 */
class SomdaLogging
{
    /**
     * @var int
     *
     * @ORM\Column(name="logid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $logid;

    /**
     * @var int
     *
     * @ORM\Column(name="datumtijd", type="bigint", nullable=false)
     */
    private $datumtijd;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     */
    private $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     */
    private $blokid;

    /**
     * @var int
     *
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    private $ip;


}
