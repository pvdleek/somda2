<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_ipb_ip_bans")
 * @ORM\Entity
 */
class IpBan
{
    /**
     * @var int
     * @ORM\Column(name="ipb_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(name="ipb_name", type="string", length=20, nullable=true)
     */
    private $name;

    /**
     * @var int|null
     * @ORM\Column(name="ipb_ip", type="bigint", nullable=true)
     */
    private $ipAddress;

    /**
     * @var int|null
     * @ORM\Column(name="ipb_datetime", type="bigint", nullable=true)
     */
    private $timestamp;

    /**
     * @var int|null
     * @ORM\Column(name="ipb_ban_hours", type="bigint", nullable=true)
     */
    private $duration;

    /**
     * @var string|null
     * @ORM\Column(name="ipb_reason", type="string", length=50, nullable=true)
     */
    private $reason;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="ipb_uid", referencedColumnName="uid")
     */
    private $user;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return IpBan
     */
    public function setId(int $id): IpBan
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return IpBan
     */
    public function setName(?string $name): IpBan
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIpAddress(): ?int
    {
        return $this->ipAddress;
    }

    /**
     * @param int|null $ipAddress
     * @return IpBan
     */
    public function setIpAddress(?int $ipAddress): IpBan
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    /**
     * @param int|null $timestamp
     * @return IpBan
     */
    public function setTimestamp(?int $timestamp): IpBan
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     * @return IpBan
     */
    public function setDuration(?int $duration): IpBan
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     * @return IpBan
     */
    public function setReason(?string $reason): IpBan
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return IpBan
     */
    public function setUser(User $user): IpBan
    {
        $this->user = $user;
        return $this;
    }
}
