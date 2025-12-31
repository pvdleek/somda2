<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrainTableRepository;
use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrainTableRepository::class)]
#[ORM\Table(
    name: 'somda_tdr',
    indexes: [
        new ORM\Index(name: 'idx_somda_tdr__tijd', columns: ['tijd']),
        new ORM\Index(name: 'idx_somda_tdr__locatieid', columns: ['locatieid']),
        new ORM\Index(name: 'idx_somda_tdr__treinid', columns: ['treinid']),
    ]
)]
class TrainTable
{
    use DateTrait;

    public const ACTION_VALUES = ['v', '-', '+', 'a'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tdrid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'orderid', type: 'integer', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $order = 1;

    #[ORM\Column(name: 'actie', length: 1, nullable: false, options: ['default' => '-'])]
    #[Assert\Choice(choices: self::ACTION_VALUES)]
    public string $action = '-';

    #[ORM\Column(name: 'tijd', type: 'smallint', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $time = 0;

    #[ORM\ManyToOne(targetEntity: TrainTableYear::class)]
    #[ORM\JoinColumn(name: 'tdr_nr', referencedColumnName: 'tdr_nr')]
    public ?TrainTableYear $train_table_year = null;

    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'treinid', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    #[ORM\ManyToOne(targetEntity: RouteOperationDays::class)]
    #[ORM\JoinColumn(name: 'rijdagenid', referencedColumnName: 'rijdagenid')]
    public ?RouteOperationDays $routeOperationDays = null;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'locatieid', referencedColumnName: 'afkid')]
    public ?Location $location = null;
}
