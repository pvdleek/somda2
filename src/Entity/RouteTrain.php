<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_tdr_trein_mat')]
class RouteTrain
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: TrainTableYear::class)]
    #[ORM\JoinColumn(name: 'tdr_nr', referencedColumnName: 'tdr_nr')]
    public ?TrainTableYear $train_table_year = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Route::class)]
    #[ORM\JoinColumn(name: 'treinid', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(name: 'posid', referencedColumnName: 'posid')]
    public ?Position $position = null;

    #[ORM\Id]
    #[ORM\Column(name: 'dag', type: 'smallint', nullable: false, options: ['default' => '1', 'unsigned' => true])]
    public int $day_number = 1;

    #[ORM\Column(name: 'spots', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $number_of_spots = 0;

    #[ORM\ManyToOne(targetEntity: TrainNamePattern::class)]
    #[ORM\JoinColumn(name: 'mat_pattern_id', referencedColumnName: 'id')]
    public ?TrainNamePattern $train_name_pattern = null;
}
