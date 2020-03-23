<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_users_onlineid")
 * @ORM\Entity
 */
class UserOnlineId
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="onlineid", type="string", length=32, nullable=false)
     */
    private $onlineId;

    /**
     * @var int
     * @ORM\Column(name="expire_datetime", type="bigint", nullable=false)
     */
    private $expireDateTime = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserOnlineId
     */
    public function setId(int $id): UserOnlineId
    {
        $this->id = $id;
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
     * @return UserOnlineId
     */
    public function setUser(User $user): UserOnlineId
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getOnlineId(): string
    {
        return $this->onlineId;
    }

    /**
     * @param string $onlineId
     * @return UserOnlineId
     */
    public function setOnlineId(string $onlineId): UserOnlineId
    {
        $this->onlineId = $onlineId;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpireDateTime(): int
    {
        return $this->expireDateTime;
    }

    /**
     * @param int $expireDateTime
     * @return UserOnlineId
     */
    public function setExpireDateTime(int $expireDateTime): UserOnlineId
    {
        $this->expireDateTime = $expireDateTime;
        return $this;
    }
}
