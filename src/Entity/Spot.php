<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="somda_spots",
 *     uniqueConstraints={@ORM\UniqueConstraint(
 *         name="unq_somda_spots__treinid_posid_locatieid_matid_uid_datum",
 *         columns={"treinid", "posid", "locatieid", "matid", "uid", "datum"}
 *     )},
 *     indexes={
 *         @ORM\Index(name="idx_somda_spots__timestamp", columns={"timestamp"}),
 *         @ORM\Index(name="idx_somda_spots__matid", columns={"matid"}),
 *         @ORM\Index(name="idx_somda_spots__datum", columns={"datum"}),
 *         @ORM\Index(name="idx_somda_spots__uid", columns={"uid"})
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
     * @ORM\Column(name="spotid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the moment the spot was saved (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the spot (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $spotDate = null;

    /**
     * @ORM\Column(name="dag", type="smallint", nullable=false, options={"unsigned"=true})
     * @JMS\Expose()
     * @OA\Property(description="Day-number of the spot (1 till 7)", type="integer")
     */
    public int $dayNumber = 1;

    /**
     * @ORM\Column(name="input_feedback_flag", type="smallint", nullable=false, options={"unsigned"=true})
     * @JMS\Exclude()
     */
    public int $inputFeedbackFlag = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Train", inversedBy="spots")
     * @ORM\JoinColumn(name="matid", referencedColumnName="matid")
     * @JMS\Expose()
     * @OA\Property(description="The spotted train", ref=@Model(type=Train::class))
     */
    public ?Train $train = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="spots")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @JMS\Expose()
     * @OA\Property(description="The spotted route", ref=@Model(type=Route::class))
     */
    public ?Route $route = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="posid", referencedColumnName="posid")
     * @JMS\Expose()
     * @OA\Property(description="The position of the spotted train", ref=@Model(type=Position::class))
     */
    public ?Position $position = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="spots")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     * @JMS\Expose()
     * @OA\Property(description="The spot-location", ref=@Model(type=Location::class))
     */
    public ?Location $location = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="spots")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @JMS\Expose()
     * @OA\Property(description="The spotter", ref=@Model(type=User::class))
     */
    public ?User $user = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SpotExtra", mappedBy="spot")
     * @JMS\Expose()
     * @OA\Property(description="Extra information for this spot", ref=@Model(type=SpotExtra::class))
     */
    public ?SpotExtra $extra = null;

    public function __construct()
    {
        $this->timestamp = new \DateTime();
    }

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
