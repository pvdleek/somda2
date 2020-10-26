<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="trn_transporter",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_trn_name", columns={"trn_name"})}
 * )
 * @ORM\Entity
 */
class Transporter
{
    /**
     * @var int|null
     * @ORM\Column(name="trn_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="trn_name", type="string", length=35, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Name of the transporter", maxLength=35, type="string")
     */
    public string $name = '';

    /**
     * @var int|null
     * @ORM\Column(name="trn_iff_code", type="integer", nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Official IFF code", type="integer")
     */
    public ?int $iffCode = null;

    /**
     * @var Train[]
     * @JMS\Exclude()
     */
    private $trains;

    /**
     * @var RouteList[]
     * @JMS\Exclude()
     */
    private $routeLists;

    /**
     *
     */
    public function __construct()
    {
        $this->trains = new ArrayCollection();
        $this->routeLists = new ArrayCollection();
    }

    /**
     * @param Train $train
     * @return Transporter
     */
    public function addTrain(Train $train): Transporter
    {
        $this->trains[] = $train;
        return $this;
    }

    /**
     * @return Train[]
     */
    public function getTrains(): array
    {
        return $this->trains->toArray();
    }

    /**
     * @param RouteList $routeList
     * @return Transporter
     */
    public function addRouteList(RouteList $routeList): Transporter
    {
        $this->routeLists[] = $routeList;
        return $this;
    }

    /**
     * @return RouteList[]
     */
    public function getRouteLists(): array
    {
        return $this->routeLists->toArray();
    }
}
