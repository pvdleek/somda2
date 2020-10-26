<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="rol_route_list", indexes={
 *     @ORM\Index(name="IDX_rol_first_number", columns={"rol_first_number"}),
 *     @ORM\Index(name="IDX_rol_last_number", columns={"rol_last_number"}),
 *     @ORM\Index(name="IDX_rol_tty_id", columns={"rol_tty_id"}),
 *     @ORM\Index(name="IDX_rol_trn_id", columns={"rol_trn_id"}),
 *     @ORM\Index(name="IDX_rol_cha_id", columns={"rol_cha_id"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\RouteList")
 */
class RouteList
{
    /**
     * @var int|null
     * @ORM\Column(name="rol_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="rol_first_number", type="integer", nullable=false, options={"default"="1"})
     * @JMS\Expose()
     * @SWG\Property(description="First number of the series", type="integer")
     */
    public int $firstNumber = 1;

    /**
     * @var int
     * @ORM\Column(name="rol_last_number", type="integer", nullable=false, options={"default"="2"})
     * @Assert\GreaterThan(propertyPath="firstNumber", message="Het eindnummer moet meer zijn dan het startnummer")
     * @JMS\Expose()
     * @SWG\Property(description="Last number of the series", type="integer")
     */
    public int $lastNumber = 2;

    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="rol_tty_id", referencedColumnName="tty_id")
     * @JMS\Exclude()
     */
    public TrainTableYear $trainTableYear;

    /**
     * @var Transporter
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="rol_trn_id", referencedColumnName="trn_id")
     * @JMS\Expose()
     * @SWG\Property(description="The transporter for this series", ref=@Model(type=Transporter::class))
     */
    public Transporter $transporter;

    /**
     * @var Characteristic
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic")
     * @ORM\JoinColumn(name="rol_cha_id", referencedColumnName="cha_id")
     * @JMS\Expose()
     * @SWG\Property(description="The characteristic for this series", ref=@Model(type=Characteristic::class))
     */
    public Characteristic $characteristic;

    /**
     * @var string|null
     * @ORM\Column(name="rol_section", type="string", length=75, nullable=true)
     * @JMS\Exclude()
     */
    public ?string $section = null;

    /**
     * @var Route[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Route", inversedBy="routeLists")
     * @ORM\JoinTable(name="rlr_route_list_route",
     *     joinColumns={@ORM\JoinColumn(name="rlr_rol_id", referencedColumnName="rol_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="rlr_rou_id", referencedColumnName="rou_id")}
     * )
     * @JMS\Exclude()
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
