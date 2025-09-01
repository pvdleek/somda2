<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_tdr_route')]
class RouteListLocations
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: TrainTableYear::class)]
    #[ORM\JoinColumn(name: 'tdr_nr', referencedColumnName: 'tdr_nr')]
    public ?TrainTableYear $train_table_year = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: RouteList::class)]
    #[ORM\JoinColumn(name: 'treinnummerlijst_id', referencedColumnName: 'id')]
    public ?RouteList $route_list = null;

    #[ORM\Id]
    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $type = 1;

    #[ORM\Id]
    #[ORM\Column(name: 'volgorde', type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $order = 1;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'locatieid', referencedColumnName: 'afkid')]
    public ?Location $location = null;
}
