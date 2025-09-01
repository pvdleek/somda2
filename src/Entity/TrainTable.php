<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrainTableRepository;
use App\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrainTableRepository::class)]
#[ORM\Table(
    name: 'somda_tdr',
    indexes: [
        new ORM\Index(name: 'idx_somda_tdr__tijd', columns: ['tijd']),
        new ORM\Index(name: 'idx_somda_tdr__locatieid', columns: ['locatieid']),
        new ORM\Index(name: 'idx_somda_tdr__treinid', columns: ['treinid']),
    ]
)]
class TrainTable
{
    use DateTrait;

    public const ACTION_VALUES = ['v', '-', '+', 'a'];

    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tdrid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The order in which the items should be displayed", type="integer")
     */
    #[ORM\Column(name: 'orderid', type: 'integer', nullable: false, options: ['default' => 1, 'unsigned' => true])]
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
    #[ORM\Column(name: 'actie', length: 1, nullable: false, options: ['default' => '-'])]
    #[Assert\Choice(choices: self::ACTION_VALUES)]
    public string $action = '-';

    /**
     * @JMS\Exclude()
     * @OA\Property(
     *     description="The time of the trainTable action (hh:mm, 24-hour clock, GMT+1 Amsterdam timezone)",
     *     property="displayTime",
     *     type="string",
     * )
     */
    #[ORM\Column(name: 'tijd', type: 'smallint', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $time = 0;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The trainTableYear to which this trainTable belongs",
     *     ref=@Model(type=TrainTableYear::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: TrainTableYear::class)]
    #[ORM\JoinColumn(name: 'tdr_nr', referencedColumnName: 'tdr_nr')]
    public ?TrainTableYear $train_table_year = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'treinid', referencedColumnName: 'treinid')]
    public ?Route $route = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The days on which this route operates",
     *     ref=@Model(type=RouteOperationDays::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: RouteOperationDays::class)]
    #[ORM\JoinColumn(name: 'rijdagenid', referencedColumnName: 'rijdagenid')]
    public ?RouteOperationDays $routeOperationDays = null;

    /**
     * @JMS\Expose()
     * @OA\Property(
     *     description="The location of the trainTable action",
     *     ref=@Model(type=Location::class),
     * )
     */
    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'train_tables')]
    #[ORM\JoinColumn(name: 'locatieid', referencedColumnName: 'afkid')]
    public ?Location $location = null;

    /**
     * @JMS\VirtualProperty(name="displayTime")
     */
    public function getDisplayTime(): string
    {
        return $this->timeDatabaseToDisplay($this->time);
    }
}
