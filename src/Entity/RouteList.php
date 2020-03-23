<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_treinnummerlijst", indexes={@ORM\Index(name="idx_48381_nr_start", columns={"nr_start"}), @ORM\Index(name="idx_48381_nr_eind", columns={"nr_eind"})})
 * @ORM\Entity
 */
class RouteList
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="nr_start", type="bigint", nullable=false)
     */
    private $firstNumber;

    /**
     * @var int
     * @ORM\Column(name="nr_eind", type="bigint", nullable=false)
     */
    private $lastNumber;

    /**
     * @var TrainTable
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     */
    private $trainTableYear;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     */
    private $transporter;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic")
     * @ORM\JoinColumn(name="karakteristiek_id", referencedColumnName="karakteristiek_id")
     */
    private $characteristic;

    /**
     * @var string|null
     * @ORM\Column(name="traject", type="string", length=75, nullable=true)
     */
    private $section;

    /**
     * @var Route[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Route", inversedBy="routeLists")
     * @ORM\JoinTable(name="somda_tdr_trein_treinnummerlijst",
     *     joinColumns={@ORM\JoinColumn(name="treinnummerlijst_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="treinid", referencedColumnName="treinid")}
     * )
     */
    private $routes;

    /**
     *
     */
    public function __construct()
    {
        $this->routes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return RouteList
     */
    public function setId(int $id): RouteList
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getFirstNumber(): int
    {
        return $this->firstNumber;
    }

    /**
     * @param int $firstNumber
     * @return RouteList
     */
    public function setFirstNumber(int $firstNumber): RouteList
    {
        $this->firstNumber = $firstNumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastNumber(): int
    {
        return $this->lastNumber;
    }

    /**
     * @param int $lastNumber
     * @return RouteList
     */
    public function setLastNumber(int $lastNumber): RouteList
    {
        $this->lastNumber = $lastNumber;
        return $this;
    }

    /**
     * @return TrainTable
     */
    public function getTrainTableYear(): TrainTable
    {
        return $this->trainTableYear;
    }

    /**
     * @param TrainTable $trainTableYear
     * @return RouteList
     */
    public function setTrainTableYear(TrainTable $trainTableYear): RouteList
    {
        $this->trainTableYear = $trainTableYear;
        return $this;
    }

    /**
     * @return Transporter
     */
    public function getTransporter(): Transporter
    {
        return $this->transporter;
    }

    /**
     * @param Transporter $transporter
     * @return RouteList
     */
    public function setTransporter(Transporter $transporter): RouteList
    {
        $this->transporter = $transporter;
        return $this;
    }

    /**
     * @return Characteristic
     */
    public function getCharacteristic(): Characteristic
    {
        return $this->characteristic;
    }

    /**
     * @param Characteristic $characteristic
     * @return RouteList
     */
    public function setCharacteristic(Characteristic $characteristic): RouteList
    {
        $this->characteristic = $characteristic;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSection(): ?string
    {
        return $this->section;
    }

    /**
     * @param string|null $section
     * @return RouteList
     */
    public function setSection(?string $section): RouteList
    {
        $this->section = $section;
        return $this;
    }

    /**
     * @param Route $route
     * @return RouteList
     */
    public function addRoute(Route $route): RouteList
    {
        $this->routes[] = $route;
        return $this;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes->toArray();
    }
}
