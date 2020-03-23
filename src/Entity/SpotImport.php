<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_in_spots", indexes={@ORM\Index(name="idx_48063_uid", columns={"uid"}), @ORM\Index(name="idx_48063_mat", columns={"mat"})})
 * @ORM\Entity
 */
class SpotImport
{
    /**
     * @var int
     * @ORM\Column(name="spotid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="extra", type="string", length=255, nullable=false)
     */
    private $extra = '';

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $date;

    /**
     * @var int
     * @ORM\Column(name="dag", type="bigint", nullable=false)
     */
    private $dayNumber;

    /**
     * @var int|null
     * @ORM\Column(name="tijd", type="bigint", nullable=true)
     */
    private $time;

    /**
     * @var string
     * @ORM\Column(name="locatie", type="string", length=15, nullable=false)
     */
    private $importLocation = '';

    /**
     * @var string
     * @ORM\Column(name="mat", type="string", length=20, nullable=false)
     */
    private $importTrain = '';

    /**
     * @var string
     * @ORM\Column(name="treinnr", type="string", length=15, nullable=false)
     */
    private $importRoute = '';

    /**
     * @var string
     * @ORM\Column(name="positie", type="string", length=2, nullable=false)
     */
    private $importPosition = '';

    /**
     * @var string|null
     * @ORM\Column(name="actie", type="string", length=100, nullable=true)
     */
    private $action;

    /**
     * @var int
     * @ORM\Column(name="spot_continue", type="bigint", nullable=false, options={"default"="1"})
     */
    private $continue = 1;

    /**
     * @var Train
     * @ORM\ManyToOne(targetEntity="App\Entity\Train")
     * @ORM\JoinColumn(name="matid", referencedColumnName="matid")
     */
    private $train;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    private $location;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    private $user;

    /**
     * @var Spot
     * @ORM\OneToOne(targetEntity="App\Entity\Spot")
     * @ORM\JoinColumn(name="spotstabel_id", referencedColumnName="spotid")
     */
    private $spot;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SpotImport
     */
    public function setId(int $id): SpotImport
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtra(): string
    {
        return $this->extra;
    }

    /**
     * @param string $extra
     * @return SpotImport
     */
    public function setExtra(string $extra): SpotImport
    {
        $this->extra = $extra;
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
     * @return SpotImport
     */
    public function setDate(DateTime $date): SpotImport
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getDayNumber(): int
    {
        return $this->dayNumber;
    }

    /**
     * @param int $dayNumber
     * @return SpotImport
     */
    public function setDayNumber(int $dayNumber): SpotImport
    {
        $this->dayNumber = $dayNumber;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @param int|null $time
     * @return SpotImport
     */
    public function setTime(?int $time): SpotImport
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string
     */
    public function getImportLocation(): string
    {
        return $this->importLocation;
    }

    /**
     * @param string $importLocation
     * @return SpotImport
     */
    public function setImportLocation(string $importLocation): SpotImport
    {
        $this->importLocation = $importLocation;
        return $this;
    }

    /**
     * @return string
     */
    public function getImportTrain(): string
    {
        return $this->importTrain;
    }

    /**
     * @param string $importTrain
     * @return SpotImport
     */
    public function setImportTrain(string $importTrain): SpotImport
    {
        $this->importTrain = $importTrain;
        return $this;
    }

    /**
     * @return string
     */
    public function getImportRoute(): string
    {
        return $this->importRoute;
    }

    /**
     * @param string $importRoute
     * @return SpotImport
     */
    public function setImportRoute(string $importRoute): SpotImport
    {
        $this->importRoute = $importRoute;
        return $this;
    }

    /**
     * @return string
     */
    public function getImportPosition(): string
    {
        return $this->importPosition;
    }

    /**
     * @param string $importPosition
     * @return SpotImport
     */
    public function setImportPosition(string $importPosition): SpotImport
    {
        $this->importPosition = $importPosition;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string|null $action
     * @return SpotImport
     */
    public function setAction(?string $action): SpotImport
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return int
     */
    public function getContinue(): int
    {
        return $this->continue;
    }

    /**
     * @param int $continue
     * @return SpotImport
     */
    public function setContinue(int $continue): SpotImport
    {
        $this->continue = $continue;
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
     * @return SpotImport
     */
    public function setTrain(Train $train): SpotImport
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
     * @return SpotImport
     */
    public function setRoute(Route $route): SpotImport
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
     * @return SpotImport
     */
    public function setPosition(Position $position): SpotImport
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
     * @return SpotImport
     */
    public function setLocation(Location $location): SpotImport
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
     * @return SpotImport
     */
    public function setUser(User $user): SpotImport
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Spot
     */
    public function getSpot(): Spot
    {
        return $this->spot;
    }

    /**
     * @param Spot $spot
     * @return SpotImport
     */
    public function setSpot(Spot $spot): SpotImport
    {
        $this->spot = $spot;
        return $this;
    }
}
