<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_banner_views', indexes: [new ORM\Index(name: 'idx_somda_banner__bannerid', columns: ['bannerid'])])]
class BannerView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(nullable: true)]
    public ?\DateTime $timestamp = null;

    #[ORM\Column(name: 'ip', type: 'bigint', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $ip_address = 0;

    #[ORM\ManyToOne(targetEntity: Banner::class, inversedBy: 'banner_views')]
    #[ORM\JoinColumn(name: 'bannerid', referencedColumnName: 'bannerid')]
    public ?Banner $banner = null;
}
