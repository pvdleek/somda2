<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_logging")
 * @ORM\Entity
 */
class Log
{
    /**
     * @var int|null
     * @ORM\Column(name="logid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="datumtijd", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var int
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    public int $ipAddress;

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
     * @var float|null
     * @ORM\Column(name="duration", type="float", precision=5, scale=2, nullable=true)
     */
    public ?float $duration;

    /**
     * @var float|null
     * @ORM\Column(name="memory_usage", type="float", precision=8, scale=3, nullable=true)
     */
    public ?float $memoryUsage;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    public ?User $user;
}
