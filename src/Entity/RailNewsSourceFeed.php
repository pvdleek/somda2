<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_snf_spoor_nieuws_bron_feed')]
class RailNewsSourceFeed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'snf_id', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'snf_url', length: 255, nullable: false, options: ['default' => ''])]
    public string $url = '';

    #[ORM\Column(name: 'snf_filter_results', nullable: false, options: ['default' => false])]
    public bool $filter_results = false;

    #[ORM\ManyToOne(targetEntity: RailNewsSource::class, inversedBy: 'feeds')]
    #[ORM\JoinColumn(name: 'snf_snb_id', referencedColumnName: 'snb_id')]
    public ?RailNewsSource $source = null;
}
