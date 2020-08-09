<?php

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="somda_tdr",
 *     indexes={
 *         @ORM\Index(name="idx_48320_tijd", columns={"tijd"}),
 *         @ORM\Index(name="idx_48320_locatieid", columns={"locatieid"}),
 *         @ORM\Index(name="idx_48320_treinid", columns={"treinid"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TrainTable")
 */
class TrainTable extends Entity
{
    use DateTrait;

    public const ACTION_VALUES = ['v', '-', '+', 'a'];

    /**
     * @var int|null
     * @ORM\Column(name="tdrid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    protected ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="orderid", type="integer", nullable=false, options={"default"="1"})
     * @JMS\Expose()
     * @SWG\Property(description="The order in which the items should be displayed", type="integer")
     */
    public int $order = 1;

    /**
     * @var string
     * @ORM\Column(name="actie", type="string", length=1, nullable=false, options={"default"="-"})
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
     * @ORM\Column(name="tijd", type="integer", nullable=false, options={"default"="0"})
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
     * @ORM\Column(name="spoor", type="string", length=3, nullable=true)
     * @JMS\Exclude()
     */
    public ?string $track;

    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
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
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @JMS\Exclude()
     */
    public Route $route;

    /**
     * @var RouteOperationDays
     * @ORM\ManyToOne(targetEntity="App\Entity\RouteOperationDays")
     * @ORM\JoinColumn(name="rijdagenid", referencedColumnName="rijdagenid")
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
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
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
