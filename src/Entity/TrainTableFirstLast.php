<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'somda_tdr_s_e')]
class TrainTableFirstLast
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: TrainTableYear::class)]
    #[ORM\JoinColumn(name: 'tdr_nr', referencedColumnName: 'tdr_nr')]
    public ?TrainTableYear $train_table_year = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'train_table_first_lasts')]
    #[ORM\JoinColumn(name: 'treinid', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    #[ORM\Id]
    #[ORM\Column(name: 'dag', type: 'smallint', options: ['default' => 1, 'unsigned' => true])]
    public int $day_number = 1;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'v_locatieid', referencedColumnName: 'afkid')]
    public ?Location $first_location = null;

    #[ORM\Column(name: 'v_actie', length: 1, options: ['default' => '-'])]
    #[Assert\Choice(choices: TrainTable::ACTION_VALUES)]
    public string $first_action = '-';

    #[ORM\Column(name: 'v_tijd', type: 'smallint', options: ['default' => 0, 'unsigned' => true])]
    public int $first_time = 0;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'a_locatieid', referencedColumnName: 'afkid')]
    public ?Location $last_location = null;

    #[ORM\Column(name: 'a_actie', length: 1, options: ['default' => '-'])]
    #[Assert\Choice(choices: TrainTable::ACTION_VALUES)]
    public string $last_action = '-';

    #[ORM\Column(name: 'a_tijd', type: 'smallint', options: ['default' => 0, 'unsigned' => true])]
    public int $last_time = 0;
}
