<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="log_log", indexes={@ORM\Index(name="IDX_log_use_id", columns={"log_use_id"})})
 * @ORM\Entity
 */
class Log
{
    /**
     * @var int|null
     * @ORM\Column(name="log_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="log_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var int
     * @ORM\Column(name="log_ip_address", type="bigint", nullable=false)
     */
    public int $ipAddress;

    /**
     * @var string
     * @ORM\Column(name="log_route", type="string", nullable=false)
     */
    public string $route;

    /**
     * @var array
     * @ORM\Column(name="log_route_parameters", type="array", nullable=false)
     */
    public array $routeParameters = [];

    /**
     * @var float|null
     * @ORM\Column(name="log_duration", type="float", precision=5, scale=2, nullable=true)
     */
    public ?float $duration;

    /**
     * @var float|null
     * @ORM\Column(name="log_memory_usage", type="float", precision=8, scale=3, nullable=true)
     */
    public ?float $memoryUsage;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="log_use_id", referencedColumnName="use_id")
     */
    public ?User $user;
}
