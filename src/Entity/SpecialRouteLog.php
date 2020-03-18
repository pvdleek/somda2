<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_drgl_logging")
 * @ORM\Entity
 */
class SpecialRouteLog
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $date;

    /**
     * @var DateTime
     * @ORM\Column(name="tijd", type="time", nullable=false)
     */
    private $time;

    /**
     * @var string
     * @ORM\Column(name="actie", type="text", length=0, nullable=false)
     */
    private $action;

    /**
     * @var SpecialRoute
     * @ORM\ManyToOne(targetEntity="App\Entity\SpecialRoute", inversedBy="logs")
     * @ORM\JoinColumn(name="drglid", referencedColumnName="drglid")
     */
    private $specialRoute;

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
     * @return SpecialRouteLog
     */
    public function setId(int $id): SpecialRouteLog
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return SpecialRouteLog
     */
    public function setDate(DateTime $date): SpecialRouteLog
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime $time
     * @return SpecialRouteLog
     */
    public function setTime(DateTime $time): SpecialRouteLog
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return SpecialRouteLog
     */
    public function setAction(string $action): SpecialRouteLog
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return SpecialRoute
     */
    public function getSpecialRoute(): SpecialRoute
    {
        return $this->specialRoute;
    }

    /**
     * @param SpecialRoute $specialRoute
     * @return SpecialRouteLog
     */
    public function setSpecialRoute(SpecialRoute $specialRoute): SpecialRouteLog
    {
        $this->specialRoute = $specialRoute;
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
     * @return SpecialRouteLog
     */
    public function setUser(User $user): SpecialRouteLog
    {
        $this->user = $user;
        return $this;
    }
}
