<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_ddar')]
#[ORM\Index(name: 'idx_somda_ddar__matid', columns: ['matid'])]
class Ddar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Train::class)]
    #[ORM\JoinColumn(name: 'matid', referencedColumnName: 'matid')]
    public ?Train $train = null;

    #[ORM\Column(name: 'stam', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    public ?int $trunk_number = null;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'afkid', referencedColumnName: 'afkid')]
    public ?Location $location = null;

    #[ORM\Column(name: 'spot_ander_laatste', type: 'date', nullable: true)]
    public ?\DateTime $timestamp_other_last = null;

    #[ORM\Column(name: 'spot_eerste', type: 'date', nullable: true)]
    public ?\DateTime $timestamp_first = null;

    #[ORM\Column(name: 'spot_laatste', type: 'date', nullable: true)]
    public ?\DateTime $timestamp_last = null;

    #[ORM\Column(name: 'spot_ander_eerste', type: 'date', nullable: true)]
    public ?\DateTime $timestamp_other_first = null;

    #[ORM\Column(length: 150, nullable: false, options: ['default' => ''])]
    public string $extra = '';
}
