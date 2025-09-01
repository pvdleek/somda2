<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SpotRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: SpotRepository::class)]
#[ORM\Table(name: 'somda_spots', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'unq_somda_spots__treinid_posid_locatieid_matid_uid_datum', columns: [
        'treinid',
        'posid',
        'locatieid',
        'matid',
        'uid',
        'datum'
    ]),
], indexes: [
    new ORM\Index(name: 'idx_somda_spots__timestamp', columns: ['timestamp']),
    new ORM\Index(name: 'idx_somda_spots__matid', columns: ['matid']),
    new ORM\Index(name: 'idx_somda_spots__datum', columns: ['datum']),
    new ORM\Index(name: 'idx_somda_spots__uid', columns: ['uid']),
])]
class Spot
{
    public const INPUT_FEEDBACK_TRAIN_NEW = 1;
    public const INPUT_FEEDBACK_TRAIN_NEW_NO_PATTERN = 2;
    public const INPUT_FEEDBACK_ROUTE_NEW = 4;
    public const INPUT_FEEDBACK_ROUTE_NOT_ON_DAY = 8;
    public const INPUT_FEEDBACK_ROUTE_NOT_ON_LOCATION = 16;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'spotid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the moment the spot was saved (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $timestamp = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the spot (Y-m-dTH:i:sP)", type="string")
     */
    #[ORM\Column(name: 'datum', type: 'datetime', nullable: true)]
    public ?\DateTime $spot_date = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Day-number of the spot (1 till 7)", type="integer")
     */
    #[ORM\Column(name: 'dag', type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $day_number = 1;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $input_feedback_flag = 0;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The spotted train", ref=@Model(type=Train::class))
     */
    #[ORM\ManyToOne(targetEntity: Train::class, inversedBy: 'spots')]
    #[ORM\JoinColumn(name: 'matid', referencedColumnName: 'matid')]
    public ?Train $train = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The spotted route", ref=@Model(type=Route::class))
     */
    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'spots')]
    #[ORM\JoinColumn(name: 'treinid', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The position of the spotted train", ref=@Model(type=Position::class))
     */
    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(name: 'posid', referencedColumnName: 'posid')]
    public ?Position $position = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The spot-location", ref=@Model(type=Location::class))
     */
    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'spots')]
    #[ORM\JoinColumn(name: 'locatieid', referencedColumnName: 'afkid')]
    public ?Location $location = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The spotter", ref=@Model(type=User::class))
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'spots')]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Extra information for this spot", ref=@Model(type=SpotExtra::class))
     */
    #[ORM\OneToOne(targetEntity: SpotExtra::class, mappedBy: 'spot')]
    public ?SpotExtra $extra = null;

    public function __construct()
    {
        $this->timestamp = new \DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'spot_date' => $this->spot_date,
            'train' => $this->train->number,
            'route' => $this->route->number,
            'position' => $this->position->name,
            'location' => $this->location->name,
            'location_description' => $this->location->description,
            'extra' => $this->extra ? $this->extra->extra : null,
            'user_extra' => $this->extra ? $this->extra->user_extra : null,
        ];
    }
}
