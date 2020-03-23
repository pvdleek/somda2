<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_sht_shout")
 * @ORM\Entity
 */
class Shout
{
    /**
     * @var int
     * @ORM\Column(name="sht_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="sht_ip", type="bigint", nullable=false)
     */
    private $ipAddress;

    /**
     * @var int
     * @ORM\Column(name="sht_datumtijd", type="bigint", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     * @ORM\Column(name="sht_text", type="string", length=255, nullable=false)
     */
    private $text;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="sht_uid", referencedColumnName="uid")
     */
    private $author;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Shout
     */
    public function setId(int $id): Shout
    {
        $this->id = $id;
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
     * @return Shout
     */
    public function setIpAddress(int $ipAddress): Shout
    {
        $this->ipAddress = $ipAddress;
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
     * @return Shout
     */
    public function setTimestamp(int $timestamp): Shout
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Shout
     */
    public function setText(string $text): Shout
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Shout
     */
    public function setAuthor(User $author): Shout
    {
        $this->author = $author;
        return $this;
    }
}
