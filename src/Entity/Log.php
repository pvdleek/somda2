<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: 'somda_logging')]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'logid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'datumtijd', type: 'datetime', nullable: true)]
    public ?\DateTime $timestamp = null;
    
    #[ORM\Column(name: 'ip', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $ip_address = 0;

    #[ORM\Column(length: 255, nullable: false, options: ['default' => ''])]
    public string $route = '';

    #[ORM\Column(name: 'route_parameters', nullable: false, options: ['default' => []])]
    public array $route_parameters = [];

    #[ORM\Column(precision: 5, scale: 2, nullable: true)]
    public ?float $duration = null;

    #[ORM\Column(precision: 8, scale: 3, nullable: true)]
    public ?float $memory_usage = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;
}
