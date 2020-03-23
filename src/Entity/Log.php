<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_logging")
 * @ORM\Entity
 */
class Log
{
    /**
     * @var int
     * @ORM\Column(name="logid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="datumtijd", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var int
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    private $ipAddress;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    private $user;

    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(name="blokid", referencedColumnName="blokid")
     */
    private $block;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Log
     */
    public function setId(int $id): Log
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return Log
     */
    public function setTimestamp(int $timestamp): Log
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getIpAddress(): int
    {
        return $this->ipAddress;
    }

    /**
     * @param int $ipAddress
     * @return Log
     */
    public function setIpAddress(int $ipAddress): Log
    {
        $this->ipAddress = $ipAddress;
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
     * @return Log
     */
    public function setUser(User $user): Log
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Block
     */
    public function getBlock(): Block
    {
        return $this->block;
    }

    /**
     * @param Block $block
     * @return Log
     */
    public function setBlock(Block $block): Log
    {
        $this->block = $block;
        return $this;
    }
}
