<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_ipb_ip_bans")
 * @ORM\Entity
 */
class IpBan extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="ipb_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @var string|null
     * @ORM\Column(name="ipb_name", type="string", length=20, nullable=true)
     */
    public ?string $name;

    /**
     * @var int|null
     * @ORM\Column(name="ipb_ip", type="bigint", nullable=true)
     */
    public ?int $ipAddress;

    /**
     * @var DateTime|null
     * @ORM\Column(name="ipb_datetime", type="datetime", nullable=true)
     */
    public ?DateTime $timestamp;

    /**
     * @var int|null
     * @ORM\Column(name="ipb_ban_hours", type="bigint", nullable=true)
     */
    public ?int $duration;

    /**
     * @var string|null
     * @ORM\Column(name="ipb_reason", type="string", length=50, nullable=true)
     */
    public ?string $reason;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="ipb_uid", referencedColumnName="uid")
     */
    public User $user;
}
