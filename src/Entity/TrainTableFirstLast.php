<?php
declare(strict_types=1);

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_tdr_s_e")
 * @ORM\Entity
 */
class TrainTableFirstLast
{
    use DateTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @ORM\Id
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?TrainTableYear $trainTableYear = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="trainTableFirstLasts")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public ?Route $route = null;

    /**
     * @ORM\Column(name="dag", type="smallint", nullable=false, options={"default"="1", "unsigned"=true})
     * @ORM\Id
     * @JMS\Expose()
     * @OA\Property(description="The day-number", enum={1,2,3,4,5,6,7}, type="integer")
     */
    public int $dayNumber = 1;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="v_locatieid", referencedColumnName="afkid")
     * @JMS\Expose()
     * @OA\Property(description="The start-location of the route", ref=@Model(type=Location::class))
     */
    public ?Location $firstLocation = null;

    /**
     * @ORM\Column(name="v_actie", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     * @JMS\Expose()
     * @OA\Property(
     *     description="The start-action of the route: 'v' for departure, '-' for a drivethrough,\
            '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    public string $firstAction = '-';

    /**
     * @ORM\Column(name="v_tijd", type="smallint", nullable=false, options={"default"="0", "unsigned"=true})
     * @JMS\Exclude()
     * @OA\Property(
     *     description="The start-time of the route (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayFirstTime",
     *     type="string",
     * )
     */
    public int $firstTime = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="a_locatieid", referencedColumnName="afkid")
     * @JMS\Expose()
     * @OA\Property(description="The end-location of the route", ref=@Model(type=Location::class))
     */
    public ?Location $lastLocation = null;

    /**
     * @ORM\Column(name="a_actie", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     * @JMS\Expose()
     * @OA\Property(
     *     description="The end-action of the route: 'v' for departure, '-' for a drivethrough,\
            '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    public string $lastAction = '-';

    /**
     * @ORM\Column(name="a_tijd", type="smallint", nullable=false, options={"default"="0", "unsigned"=true})
     * @JMS\Exclude()
     * @OA\Property(
     *     description="The end-time of the route (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayLastTime",
     *     type="string",
     * )
     */
    public int $lastTime = 0;

    /**
     * @JMS\VirtualProperty(name="displayFirstTime")
     */
    public function getDisplayFirstTime(): string
    {
        return $this->timeDatabaseToDisplay($this->firstTime);
    }

    /**
     * @JMS\VirtualProperty(name="displayLastTime")
     */
    public function getDisplayLastTime(): string
    {
        return $this->timeDatabaseToDisplay($this->lastTime);
    }
}
