<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mobiel_logging")
 * @ORM\Entity
 */
class LogMobile
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=true)
     */
    private $userAgent;

    /**
     * @var int|null
     * @ORM\Column(name="datetime", type="bigint", nullable=true)
     */
    private $datetime;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
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
     * @return LogMobile
     */
    public function setId(int $id): LogMobile
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent
     * @return LogMobile
     */
    public function setUserAgent(?string $userAgent): LogMobile
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDatetime(): ?int
    {
        return $this->datetime;
    }

    /**
     * @param int|null $datetime
     * @return LogMobile
     */
    public function setDatetime(?int $datetime): LogMobile
    {
        $this->datetime = $datetime;
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
     * @return LogMobile
     */
    public function setUser(User $user): LogMobile
    {
        $this->user = $user;
        return $this;
    }
}
