<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spots", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48259_treinid", columns={"treinid", "posid", "locatieid", "matid", "uid", "datum"})}, indexes={@ORM\Index(name="idx_48259_matid", columns={"matid"}), @ORM\Index(name="idx_48259_datum", columns={"datum"}), @ORM\Index(name="idx_48259_uid", columns={"uid"})})
 * @ORM\Entity
 */
class Spot
{
    /**
     * @var int
     * @ORM\Column(name="spotid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var SpotImport
     * @ORM\OneToOne(targetEntity="App\Entity\SpotImport")
     * @ORM\JoinColumn(name="in_spotid", referencedColumnName="spotid")
     */
    private $spotImport;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $date;

    /**
     * @var Train
     * @ORM\ManyToOne(targetEntity="App\Entity\Train", inversedBy="spots")
     * @ORM\JoinColumn(name="matid", referencedColumnName="matid")
     */
    private $train;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="spots")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     */
    private $route;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="posid", referencedColumnName="posid")
     */
    private $position;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="spots")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    private $location;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="spots")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    private $user;

    /**
     * @var SpotExtra
     * @ORM\OneToOne(targetEntity="App\Entity\SpotExtra", mappedBy="spot")
     */
    private $extra;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Spot
     */
    public function setId(int $id): Spot
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return SpotImport
     */
    public function getSpotImport(): SpotImport
    {
        return $this->spotImport;
    }

    /**
     * @param SpotImport $spotImport
     * @return Spot
     */
    public function setSpotImport(SpotImport $spotImport): Spot
    {
        $this->spotImport = $spotImport;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Spot
     */
    public function setDate(DateTime $date): Spot
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return Train
     */
    public function getTrain(): Train
    {
        return $this->train;
    }

    /**
     * @param Train $train
     * @return Spot
     */
    public function setTrain(Train $train): Spot
    {
        $this->train = $train;
        return $this;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @param Route $route
     * @return Spot
     */
    public function setRoute(Route $route): Spot
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @param Position $position
     * @return Spot
     */
    public function setPosition(Position $position): Spot
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     * @return Spot
     */
    public function setLocation(Location $location): Spot
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Spot
     */
    public function setUser(User $user): Spot
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return SpotExtra
     */
    public function getExtra(): SpotExtra
    {
        return $this->extra;
    }

    /**
     * @param SpotExtra $extra
     * @return Spot
     */
    public function setExtra(SpotExtra $extra): Spot
    {
        $this->extra = $extra;
        return $this;
    }
}
