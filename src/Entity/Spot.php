<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="spo_spot",
 *     uniqueConstraints={@ORM\UniqueConstraint(
 *         name="UNQ_spo_tra_id_pos_id_loc_id_rou_id_spotter_use_id_timestamp",
 *         columns={"spo_tra_id", "spo_pos_id", "spo_loc_id", "spo_rou_id", "spo_spotter_use_id", "spo_timestamp"}
 *     )},
 *     indexes={
 *         @ORM\Index(name="IDX_spo_tra_id", columns={"spo_tra_id"}),
 *         @ORM\Index(name="IDX_spo_rou_id", columns={"spo_rou_id"}),
 *         @ORM\Index(name="IDX_spo_pos_id", columns={"spo_pos_id"}),
 *         @ORM\Index(name="IDX_spo_loc_id", columns={"spo_loc_id"}),
 *         @ORM\Index(name="IDX_spo_timestamp", columns={"spo_timestamp"}),
 *         @ORM\Index(name="IDX_spo_spotter_use_id", columns={"spo_spotter_use_id"}),
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\Spot")
 */
class Spot
{
    public const INPUT_FEEDBACK_TRAIN_NEW = 1;
    public const INPUT_FEEDBACK_TRAIN_NEW_NO_PATTERN = 2;
    public const INPUT_FEEDBACK_ROUTE_NEW = 4;
    public const INPUT_FEEDBACK_ROUTE_NOT_ON_DAY = 8;
    public const INPUT_FEEDBACK_ROUTE_NOT_ON_LOCATION = 16;

    /**
     * @var int|null
     * @ORM\Column(name="spo_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="spo_timestamp", type="datetime", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the moment the spot was saved (Y-m-dTH:i:sP)", type="string")
     */
    public DateTime $timestamp;

    /**
     * @var DateTime
     * @ORM\Column(name="spo_spot_date", type="datetime", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the spot (Y-m-dTH:i:sP)", type="string")
     */
    public DateTime $spotDate;

    /**
     * @var integer
     * @ORM\Column(name="spo_input_feedback_flag", type="integer", nullable=false)
     * @JMS\Exclude()
     */
    public int $inputFeedbackFlag = 0;

    /**
     * @var Train
     * @ORM\ManyToOne(targetEntity="App\Entity\Train", inversedBy="spots")
     * @ORM\JoinColumn(name="spo_tra_id", referencedColumnName="tra_id")
     * @JMS\Expose()
     * @SWG\Property(description="The spotted train", ref=@Model(type=Train::class))
     */
    public Train $train;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="spots")
     * @ORM\JoinColumn(name="spo_rou_id", referencedColumnName="rou_id")
     * @JMS\Expose()
     * @SWG\Property(description="The spotted route", ref=@Model(type=Route::class))
     */
    public Route $route;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="spo_pos_id", referencedColumnName="pos_id")
     * @JMS\Expose()
     * @SWG\Property(description="The position of the spotted train", ref=@Model(type=Position::class))
     */
    public Position $position;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="spots")
     * @ORM\JoinColumn(name="spo_loc_id", referencedColumnName="loc_id")
     * @JMS\Expose()
     * @SWG\Property(description="The spot-location", ref=@Model(type=Location::class))
     */
    public Location $location;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="spots")
     * @ORM\JoinColumn(name="spo_spotter_use_id", referencedColumnName="use_id")
     * @JMS\Expose()
     * @SWG\Property(description="The spotter", ref=@Model(type=User::class))
     */
    public User $user;

    /**
     * @var SpotExtra|null
     * @ORM\OneToOne(targetEntity="App\Entity\SpotExtra", mappedBy="spot")
     * @JMS\Expose()
     * @SWG\Property(description="Extra information for this spot", ref=@Model(type=SpotExtra::class))
     */
    public ?SpotExtra $extra = null;

    /**
     *
     */
    public function __construct()
    {
        $this->timestamp = new DateTime();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'spotDate' => $this->spotDate,
            'train' => $this->train->number,
            'route' => $this->route->number,
            'position' => $this->position->name,
            'location' => $this->location->name,
            'locationDescription' => $this->location->description,
            'extra' => $this->extra ? $this->extra->extra : null,
            'userExtra' => $this->extra ? $this->extra->userExtra : null,
        ];
    }
}
