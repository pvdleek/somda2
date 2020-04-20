<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_treinnummerlijst", indexes={@ORM\Index(name="idx_48381_nr_start", columns={"nr_start"}), @ORM\Index(name="idx_48381_nr_eind", columns={"nr_eind"})})
 * @ORM\Entity
 */
class RouteList extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="nr_start", type="bigint", nullable=false)
     */
    public $firstNumber;

    /**
     * @var int
     * @ORM\Column(name="nr_eind", type="bigint", nullable=false)
     */
    public $lastNumber;

    /**
     * @var TrainTable
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     */
    public $trainTableYear;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     */
    public $transporter;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic")
     * @ORM\JoinColumn(name="karakteristiek_id", referencedColumnName="karakteristiek_id")
     */
    public $characteristic;

    /**
     * @var string|null
     * @ORM\Column(name="traject", type="string", length=75, nullable=true)
     */
    public $section;

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
