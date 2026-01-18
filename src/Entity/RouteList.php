<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RouteListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RouteListRepository::class)]
#[ORM\Table(name: 'somda_tdr_treinnummerlijst')]
#[ORM\Index(name: 'idx_somda_tdr_treinnummerlijst__nr_start', columns: ['nr_start'])]
#[ORM\Index(name: 'idx_somda_tdr_treinnummerlijst__nr_eind', columns: ['nr_eind'])]
class RouteList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'nr_start', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $first_number = 1;

    #[ORM\Column(name: 'nr_eind', nullable: false, options: ['default' => 2, 'unsigned' => true])]
    #[Assert\GreaterThan(propertyPath: 'first_number', message: 'Het eindnummer moet meer zijn dan het startnummer')]
    public int $last_number = 2;

    #[ORM\ManyToOne(targetEntity: TrainTableYear::class)]
    #[ORM\JoinColumn(name: 'tdr_nr', referencedColumnName: 'tdr_nr')]
    public ?TrainTableYear $train_table_year = null;

    #[ORM\ManyToOne(targetEntity: Transporter::class, inversedBy: 'route_lists')]
    #[ORM\JoinColumn(name: 'vervoerder_id', referencedColumnName: 'vervoerder_id')]
    public ?Transporter $transporter = null;

    #[ORM\ManyToOne(targetEntity: Characteristic::class)]
    #[ORM\JoinColumn(name: 'karakteristiek_id', referencedColumnName: 'karakteristiek_id')]
    public ?Characteristic $characteristic = null;

    #[ORM\Column(name: 'traject', type: 'string', length: 75, nullable: true)]
    public ?string $section = null;

    #[ORM\ManyToMany(targetEntity: Route::class, inversedBy: 'route_lists')]
    #[ORM\JoinTable(name: 'somda_tdr_trein_treinnummerlijst')]
    #[ORM\JoinColumn(name: 'treinnummerlijst_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'treinid', referencedColumnName: 'treinid')]
    private Collection $routes;

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
