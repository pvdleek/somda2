<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaIpbIpBans
 *
 * @ORM\Table(name="somda_ipb_ip_bans")
 * @ORM\Entity
 */
class SomdaIpbIpBans
{
    /**
     * @var int
     *
     * @ORM\Column(name="ipb_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ipbId;

    /**
     * @var int
     *
     * @ORM\Column(name="ipb_uid", type="bigint", nullable=false)
     */
    private $ipbUid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ipb_name", type="string", length=20, nullable=true)
     */
    private $ipbName;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ipb_ip", type="bigint", nullable=true)
     */
    private $ipbIp;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ipb_datetime", type="bigint", nullable=true)
     */
    private $ipbDatetime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ipb_ban_hours", type="bigint", nullable=true)
     */
    private $ipbBanHours;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ipb_reason", type="string", length=50, nullable=true)
     */
    private $ipbReason;


}
