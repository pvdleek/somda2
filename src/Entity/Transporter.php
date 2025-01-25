<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="somda_vervoerder",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unq_somda_vervoerder__omschrijving", columns={"omschrijving"})}
 * )
 * @ORM\Entity
 */
class Transporter
{
    /**
     * @ORM\Column(name="vervoerder_id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="omschrijving", type="string", length=50, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Name of the transporter", maxLength=50, type="string")
     */
    public string $name = '';

    /**
     * @ORM\Column(name="iff_code", type="integer", nullable=true, options={"unsigned"=true})
     * @JMS\Expose()
     * @OA\Property(description="Official IFF code", type="integer")
     */
    public ?int $iffCode = null;

    /**
     * @JMS\Exclude()
     */
    private $trains;

    /**
     * @JMS\Exclude()
     */
    private $routeLists;

    public function __construct()
    {
        $this->trains = new ArrayCollection();
        $this->routeLists = new ArrayCollection();
    }

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
