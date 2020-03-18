<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaShtShout
 *
 * @ORM\Table(name="somda_sht_shout")
 * @ORM\Entity
 */
class SomdaShtShout
{
    /**
     * @var int
     *
     * @ORM\Column(name="sht_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $shtId;

    /**
     * @var int
     *
     * @ORM\Column(name="sht_uid", type="bigint", nullable=false)
     */
    private $shtUid;

    /**
     * @var int
     *
     * @ORM\Column(name="sht_ip", type="bigint", nullable=false)
     */
    private $shtIp;

    /**
     * @var int
     *
     * @ORM\Column(name="sht_datumtijd", type="bigint", nullable=false)
     */
    private $shtDatumtijd;

    /**
     * @var string
     *
     * @ORM\Column(name="sht_text", type="string", length=255, nullable=false)
     */
    private $shtText;


}
