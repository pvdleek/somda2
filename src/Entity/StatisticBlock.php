<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_stats_blokken')]
class StatisticBlock
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Block::class)]
    #[ORM\JoinColumn(name: 'blokid', referencedColumnName: 'blokid')]
    public Block $block;

    #[ORM\Id]
    #[ORM\Column(type: 'date', nullable: false)]
    public \DateTime $date;

    #[ORM\Column(name: 'pageviews', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $views = 0;
}
