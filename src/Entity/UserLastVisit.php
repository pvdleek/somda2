<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_users_lastvisit")
 * @ORM\Entity
 */
class UserLastVisit
{
    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="lastVisit")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=10, nullable=false)
     */
    private $username = '';

    /**
     * @var string
     * @ORM\Column(name="real_name", type="string", length=40, nullable=false)
     */
    private $realName = '';

    /**
     * @var DateTime|null
     * @ORM\Column(name="lastvisit", type="datetime", nullable=true)
     */
    private $lastVisit;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserLastVisit
     */
    public function setUser(User $user): UserLastVisit
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return UserLastVisit
     */
    public function setUsername(string $username): UserLastVisit
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getRealName(): string
    {
        return $this->realName;
    }

    /**
     * @param string $realName
     * @return UserLastVisit
     */
    public function setRealName(string $realName): UserLastVisit
    {
        $this->realName = $realName;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastVisit(): ?DateTime
    {
        return $this->lastVisit;
    }

    /**
     * @param DateTime|null $lastVisit
     * @return UserLastVisit
     */
    public function setLastVisit(?DateTime $lastVisit): UserLastVisit
    {
        $this->lastVisit = $lastVisit;
        return $this;
    }
}
