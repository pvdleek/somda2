<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_tdr_s_e')]
class TrainTableFirstLast
{
    use DateTrait;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: TrainTableYear::class)]
    #[ORM\JoinColumn(name: 'tdr_nr', referencedColumnName: 'tdr_nr')]
    public ?TrainTableYear $train_table_year = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'train_table_first_lasts')]
    #[ORM\JoinColumn(name: 'treinid', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The day-number", enum={1,2,3,4,5,6,7}, type="integer")
     */
    #[ORM\Id]
    #[ORM\Column(name: 'dag', type: 'smallint', options: ['default' => 1, 'unsigned' => true])]
    public int $day_number = 1;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The start-location of the route", ref=@Model(type=Location::class))
     */
    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'v_locatieid', referencedColumnName: 'afkid')]
    public ?Location $first_location = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The start-action of the route: 'v' for departure, '-' for a drivethrough,\
            '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    #[ORM\Column(name: 'v_actie', length: 1, options: ['default' => '-'])]
    #[Assert\Choice(choices: TrainTable::ACTION_VALUES)]
    public string $first_action = '-';

    /**
     * @JMS\Exclude()
     * @OA\Property(
     *     description="The start-time of the route (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayFirstTime",
     *     type="string",
     * )
     */
    #[ORM\Column(name: 'v_tijd', type: 'smallint', options: ['default' => 0, 'unsigned' => true])]
    public int $first_time = 0;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The end-location of the route", ref=@Model(type=Location::class))
     */
    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(name: 'a_locatieid', referencedColumnName: 'afkid')]
    public ?Location $last_location = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The end-action of the route: 'v' for departure, '-' for a drivethrough,\
            '+' for a short stop and 'a' for arrival",
     *     maxLength=1,
     *     enum={"v","-","+","a"},
     *     type="string",
     * )
     */
    #[ORM\Column(name: 'a_actie', length: 1, options: ['default' => '-'])]
    #[Assert\Choice(choices: TrainTable::ACTION_VALUES)]
    public string $last_action = '-';

    /**
     * @JMS\Exclude()
     * @OA\Property(
     *     description="The end-time of the route (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayLastTime",
     *     type="string",
     * )
     */
    #[ORM\Column(name: 'a_tijd', type: 'smallint', options: ['default' => 0, 'unsigned' => true])]
    public int $last_time = 0;

    /**
     * @JMS\VirtualProperty(name="displayFirstTime")
     */
    public function getDisplayFirstTime(): string
    {
        return $this->timeDatabaseToDisplay($this->first_time);
    }

    /**
     * @JMS\VirtualProperty(name="displayLastTime")
     */
    public function getDisplayLastTime(): string
    {
        return $this->timeDatabaseToDisplay($this->last_time);
    }
}
