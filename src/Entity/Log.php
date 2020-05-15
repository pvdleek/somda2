<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_logging")
 * @ORM\Entity
 */
class Log extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="logid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="datumtijd", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    public string $ipAddress;

    /**
     * @var string
     * @ORM\Column(name="route", type="string", nullable=false)
     */
    public string $route;

    /**
     * @var array
     * @ORM\Column(name="route_parameters", type="array", nullable=false)
     */
    public array $routeParameters = [];

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    public ?User $user;
}
