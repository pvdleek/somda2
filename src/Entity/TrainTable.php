<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
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
    public const ACTION_VALUES = ['v', '-', '+', 'a'];

    /**
     * @var int
     * @ORM\Column(name="tdrid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="orderid", type="integer", nullable=false, options={"default"="1"})
     */
    public int $order = 1;

    /**
     * @var string
     * @ORM\Column(name="actie", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     */
    public string $action = '-';

    /**
     * @var int
     * @ORM\Column(name="tijd", type="integer", nullable=false, options={"default"="0"})
     */
    public int $time = 0;

    /**
     * @var string|null
     * @ORM\Column(name="spoor", type="string", length=3, nullable=true)
     */
    public ?string $track;

    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
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
     */
    public RouteOperationDays $routeOperationDays;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="trainTables")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    public Location $location;
}
