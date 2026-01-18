<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'ott_official_train_table')]
#[ORM\Index(name: 'idx_ott__time', columns: ['ott_time'])]
#[ORM\Index(name: 'idx_ott__ofo_id', columns: ['ott_ofo_id'])]
#[ORM\Index(name: 'idx_ott__location_id', columns: ['ott_location_id'])]
#[ORM\Index(name: 'idx_ott__route_id', columns: ['ott_route_id'])]
class OfficialTrainTable
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ott_id', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'ott_order', type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $order = 1;

    #[ORM\Column(name: 'ott_action', length: 1, nullable: false, options: ['default' => '-'])]
    #[Assert\Choice(choices: TrainTable::ACTION_VALUES)]
    public string $action = '-';

    #[ORM\Column(name: 'ott_time', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    public ?int $time = null;

    #[ORM\Column(name: 'ott_track', length: 3, nullable: true)]
    public ?string $track = null;

    #[ORM\ManyToOne(targetEntity: OfficialFootnote::class)]
    #[ORM\JoinColumn(name: 'ott_ofo_id', referencedColumnName: 'ofo_id')]
    public ?OfficialFootnote $footnote = null;

    #[ORM\ManyToOne(targetEntity: Transporter::class)]
    #[ORM\JoinColumn(name: 'ott_transporter_id', referencedColumnName: 'vervoerder_id')]
    public ?Transporter $transporter = null;

    #[ORM\ManyToOne(targetEntity: Characteristic::class)]
    #[ORM\JoinColumn(name: 'ott_characteristic_id', referencedColumnName: 'karakteristiek_id')]
    public ?Characteristic $characteristic = null;

    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'ott_route_id', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'ott_location_id', referencedColumnName: 'afkid')]
    public ?Location $location = null;
}
