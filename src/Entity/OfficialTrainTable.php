<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'ott_official_train_table', indexes: [
    new ORM\Index(name: 'idx_ott__time', columns: ['ott_time']),
    new ORM\Index(name: 'idx_ott__ofo_id', columns: ['ott_ofo_id']),
    new ORM\Index(name: 'idx_ott__location_id', columns: ['ott_location_id']),
    new ORM\Index(name: 'idx_ott__route_id', columns: ['ott_route_id']),
])]
class OfficialTrainTable
{
    use DateTrait;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ott_id', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The order in which the items should be displayed", type="integer")
     */
    #[ORM\Column(name: 'ott_order', type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $order = 1;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The action on the location: 'v' for departure, '-' for a drivethrough,\
             '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    #[ORM\Column(name: 'ott_action', length: 1, nullable: false, options: ['default' => '-'])]
    #[Assert\Choice(choices: TrainTable::ACTION_VALUES)]
    public string $action = '-';

    /**
     * @JMS\Exclude()
     * @OA\Property(
     *     description="The time of the trainTable action (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayTime",
     *     type="string",
     * )
     */
    #[ORM\Column(name: 'ott_time', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    public ?int $time = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(name: 'ott_track', length: 3, nullable: true)]
    public ?string $track = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The footnote to which this trainTable belongs",
     *     ref=@Model(type=OfficialFootnote::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: OfficialFootnote::class)]
    #[ORM\JoinColumn(name: 'ott_ofo_id', referencedColumnName: 'ofo_id')]
    public ?OfficialFootnote $footnote = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The transporter of this trainTable",
     *     ref=@Model(type=Transporter::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: Transporter::class)]
    #[ORM\JoinColumn(name: 'ott_transporter_id', referencedColumnName: 'vervoerder_id')]
    public ?Transporter $transporter = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The characteristic of this trainTable",
     *     ref=@Model(type=Characteristic::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: Characteristic::class)]
    #[ORM\JoinColumn(name: 'ott_characteristic_id', referencedColumnName: 'karakteristiek_id')]
    public ?Characteristic $characteristic = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'ott_route_id', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The location of the trainTable action",
     *     ref=@Model(type=Location::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'ott_location_id', referencedColumnName: 'afkid')]
    public ?Location $location = null;

    /**
     * @JMS\VirtualProperty(name="displayTime")
     */
    public function getDisplayTime(): string
    {
        return $this->timeDatabaseToDisplay($this->time);
    }
}
