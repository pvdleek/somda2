<?php
declare(strict_types=1);

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="ttf_train_table_first_last", indexes={
 *     @ORM\Index(name="IDX_ttf_tty_id", columns={"ttf_tty_id"}),
 *     @ORM\Index(name="IDX_ttf_rou_id", columns={"ttf_rou_id"}),
 *     @ORM\Index(name="IDX_ttf_first_loc_id", columns={"ttf_first_loc_id"}),
 *     @ORM\Index(name="IDX_ttf_last_loc_id", columns={"ttf_last_loc_id"}),
 * })
 * @ORM\Entity
 */
class TrainTableFirstLast
{
    use DateTrait;

    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="ttf_tty_id", referencedColumnName="tty_id")
     * @ORM\Id
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public TrainTableYear $trainTableYear;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="trainTableFirstLasts")
     * @ORM\JoinColumn(name="ttf_rou_id", referencedColumnName="rou_id")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public Route $route;

    /**
     * @var int
     * @ORM\Column(name="ttf_day_number", type="integer", nullable=false, options={"default"="1"})
     * @ORM\Id
     * @JMS\Expose()
     * @SWG\Property(description="The day-number", enum={1,2,3,4,5,6,7}, type="integer")
     */
    public int $dayNumber = 1;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="ttf_first_loc_id", referencedColumnName="loc_id")
     * @JMS\Expose()
     * @SWG\Property(description="The start-location of the route", ref=@Model(type=Location::class))
     */
    public Location $firstLocation;

    /**
     * @var string
     * @ORM\Column(name="ttf_first_action", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The start-action of the route: 'v' for departure, '-' for a drivethrough,\
            '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    public string $firstAction = '-';

    /**
     * @var int
     * @ORM\Column(name="ttf_first_time", type="integer", nullable=false, options={"default"="0"})
     * @JMS\Exclude()
     * @SWG\Property(
     *     description="The start-time of the route (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayFirstTime",
     *     type="string",
     * )
     */
    public int $firstTime = 0;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="ttf_last_loc_id", referencedColumnName="loc_id")
     * @JMS\Expose()
     * @SWG\Property(description="The end-location of the route", ref=@Model(type=Location::class))
     */
    public Location $lastLocation;

    /**
     * @var string
     * @ORM\Column(name="ttf_last_action", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     * @JMS\Expose()
     * @SWG\Property(
     *     description="The end-action of the route: 'v' for departure, '-' for a drivethrough,\
            '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    public string $lastAction = '-';

    /**
     * @var int
     * @ORM\Column(name="ttf_last_time", type="integer", nullable=false, options={"default"="0"})
     * @JMS\Exclude()
     * @SWG\Property(
     *     description="The end-time of the route (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayLastTime",
     *     type="string",
     * )
     */
    public int $lastTime = 0;


    /**
     * @return string
     * @JMS\VirtualProperty(name="displayFirstTime")
     */
    public function getDisplayFirstTime(): string
    {
        return $this->timeDatabaseToDisplay($this->firstTime);
    }


    /**
     * @return string
     * @JMS\VirtualProperty(name="displayLastTime")
     */
    public function getDisplayLastTime(): string
    {
        return $this->timeDatabaseToDisplay($this->lastTime);
    }
}
