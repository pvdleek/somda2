<?php

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="trt_train_table", indexes={
 *     @ORM\Index(name="IDX_trt_time", columns={"trt_time"}),
 *     @ORM\Index(name="IDX_trt_loc_id", columns={"trt_loc_id"}),
 *     @ORM\Index(name="IDX_trt_rou_id", columns={"trt_rou_id"}),
 *     @ORM\Index(name="IDX_trt_tty_id", columns={"trt_tty_id"}),
 *     @ORM\Index(name="IDX_trt_rod_id", columns={"trt_rod_id"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\TrainTable")
 */
class TrainTable
{
    use DateTrait;

    public const ACTION_VALUES = ['v', '-', '+', 'a'];

    /**
     * @var int|null
     * @ORM\Column(name="trt_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="trt_order", type="integer", nullable=false, options={"default"="1"})
     * @JMS\Expose()
     * @SWG\Property(description="The order in which the items should be displayed", type="integer")
     */
    public int $order = 1;

    /**
     * @var string
     * @ORM\Column(name="trt_action", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The action on the location: 'v' for departure, '-' for a drivethrough,\
             '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    public string $action = '-';

    /**
     * @var int
     * @ORM\Column(name="trt_time", type="integer", nullable=false, options={"default"="0"})
     * @JMS\Exclude()
     * @SWG\Property(
     *     description="The time of the trainTable action (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayTime",
     *     type="string",
     * )
     */
    public int $time = 0;

    /**
     * @var string|null
     * @ORM\Column(name="trt_track", type="string", length=3, nullable=true)
     * @JMS\Exclude()
     */
    public ?string $track;

    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="trt_tty_id", referencedColumnName="tty_id")
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The trainTableYear to which this trainTable belongs",
     *     ref=@Model(type=TrainTableYear::class),
     * )
     */
    public TrainTableYear $trainTableYear;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="trainTables")
     * @ORM\JoinColumn(name="trt_rou_id", referencedColumnName="rou_id")
     * @JMS\Exclude()
     */
    public Route $route;

    /**
     * @var RouteOperationDays
     * @ORM\ManyToOne(targetEntity="App\Entity\RouteOperationDays")
     * @ORM\JoinColumn(name="trt_rod_id", referencedColumnName="rod_id")
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The days on which this route operates",
     *     ref=@Model(type=RouteOperationDays::class),
     * )
     */
    public RouteOperationDays $routeOperationDays;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="trainTables")
     * @ORM\JoinColumn(name="trt_loc_id", referencedColumnName="loc_id")
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The location of the trainTable action",
     *     ref=@Model(type=Location::class),
     * )
     */
    public Location $location;

    /**
     * @return string
     * @JMS\VirtualProperty(name="displayTime")
     */
    public function getDisplayTime(): string
    {
        return $this->timeDatabaseToDisplay($this->time);
    }
}
