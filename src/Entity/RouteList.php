<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="somda_tdr_treinnummerlijst",
 *     indexes={
 *         @ORM\Index(name="idx_48381_nr_start", columns={"nr_start"}),
 *         @ORM\Index(name="idx_48381_nr_eind", columns={"nr_eind"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\RouteList")
 */
class RouteList
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="nr_start", type="integer", nullable=false, options={"default"="1", "unsigned"=true})
     * @JMS\Expose()
     * @OA\Property(description="First number of the series", type="integer")
     */
    public int $firstNumber = 1;

    /**
     * @ORM\Column(name="nr_eind", type="integer", nullable=false, options={"default"="2", "unsigned"=true})
     * @Assert\GreaterThan(propertyPath="firstNumber", message="Het eindnummer moet meer zijn dan het startnummer")
     * @JMS\Expose()
     * @OA\Property(description="Last number of the series", type="integer")
     */
    public int $lastNumber = 2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @JMS\Exclude()
     */
    public ?TrainTableYear $trainTableYear = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     * @JMS\Expose()
     * @OA\Property(description="The transporter for this series", ref=@Model(type=Transporter::class))
     */
    public ?Transporter $transporter = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic")
     * @ORM\JoinColumn(name="karakteristiek_id", referencedColumnName="karakteristiek_id")
     * @JMS\Expose()
     * @OA\Property(description="The characteristic for this series", ref=@Model(type=Characteristic::class))
     */
    public ?Characteristic $characteristic = null;

    /**
     * @ORM\Column(name="traject", type="string", length=75, nullable=true)
     * @JMS\Exclude()
     */
    public ?string $section = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Route", inversedBy="routeLists")
     * @ORM\JoinTable(name="somda_tdr_trein_treinnummerlijst",
     *     joinColumns={@ORM\JoinColumn(name="treinnummerlijst_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="treinid", referencedColumnName="treinid")}
     * )
     * @JMS\Exclude()
     */
    private $routes;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
    }

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

    public function removeRoute(Route $route): RouteList
    {
        $this->routes->removeElement($route);
        return $this;
    }
}
