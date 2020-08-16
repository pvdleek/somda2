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
 *     name="ott_official_train_table",
 *     indexes={
 *         @ORM\Index(name="idx_ott_time", columns={"ott_time"}),
 *         @ORM\Index(name="idx_ott_ofo_id", columns={"ott_ofo_id"}),
 *         @ORM\Index(name="idx_ott_location_id", columns={"ott_location_id"}),
 *         @ORM\Index(name="idx_ott_route_id", columns={"ott_route_id"})
 *     }
 * )
 * @ORM\Entity
 */
class OfficialTrainTable
{
    use DateTrait;

    /**
     * @var int|null
     * @ORM\Column(name="ott_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="ott_order", type="integer", nullable=false, options={"default"="1"})
     * @JMS\Expose()
     * @SWG\Property(description="The order in which the items should be displayed", type="integer")
     */
    public int $order = 1;

    /**
     * @var string
     * @ORM\Column(name="ott_action", type="string", length=1, nullable=false, options={"default"="-"})
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
     * @var int|null
     * @ORM\Column(name="ott_time", type="integer", nullable=true)
     * @JMS\Exclude()
     * @SWG\Property(
     *     description="The time of the trainTable action (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayTime",
     *     type="string",
     * )
     */
    public ?int $time = null;

    /**
     * @var string|null
     * @ORM\Column(name="ott_track", type="string", length=3, nullable=true)
     * @JMS\Exclude()
     */
    public ?string $track;

    /**
     * @var OfficialFootnote
     * @ORM\ManyToOne(targetEntity="App\Entity\OfficialFootnote")
     * @ORM\JoinColumn(name="ott_ofo_id", referencedColumnName="ofo_id")
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The footnote to which this trainTable belongs",
     *     ref=@Model(type=OfficialFootnote::class),
     * )
     */
    public OfficialFootnote $footnote;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="ott_transporter_id", referencedColumnName="vervoerder_id")
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The transporter of this trainTable",
     *     ref=@Model(type=Transporter::class),
     * )
     */
    public Transporter $transporter;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic")
     * @ORM\JoinColumn(name="ott_characteristic_id", referencedColumnName="karakteristiek_id")
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The characteristic of this trainTable",
     *     ref=@Model(type=Characteristic::class),
     * )
     */
    public Characteristic $characteristic;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="trainTables")
     * @ORM\JoinColumn(name="ott_route_id", referencedColumnName="treinid")
     * @JMS\Exclude()
     */
    public Route $route;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="trainTables")
     * @ORM\JoinColumn(name="ott_location_id", referencedColumnName="afkid")
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
