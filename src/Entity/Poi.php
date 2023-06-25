<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spot_punt")
 * @ORM\Entity
 */
class Poi
{
    /**
     * @ORM\Column(name="puntid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    public string $name = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="pois")
     * @ORM\JoinColumn(name="afkid_locatie", referencedColumnName="afkid")
     */
    public ?Location $location = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_1", referencedColumnName="afkid")
     */
    public ?Location $locationSection1 = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_2", referencedColumnName="afkid")
     */
    public ?Location $locationSection2 = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_dks", referencedColumnName="afkid")
     */
    public ?Location $locationForRoutes = null;

    /**
     * @ORM\Column(name="kilometrering", type="string", length=25, nullable=true)
     */
    public ?string $kilometre = null;

    /**
     * @ORM\Column(name="gps", type="string", length=25, nullable=true)
     */
    public ?string $gps = null;

    /**
     * @ORM\Column(name="zonstand_winter", type="string", length=50, nullable=true)
     */
    public ?string $sunPositionWinter = null;

    /**
     * @ORM\Column(name="zonstand_zomer", type="string", length=50, nullable=true)
     */
    public ?string $sunPositionSummer = null;

    /**
     * @ORM\Column(name="google_url", type="string", length=200, nullable=true)
     */
    public ?string $googleUrl = null;

    /**
     * @ORM\Column(name="foto", type="string", length=25, nullable=false, options={"default"="geen_foto.jpg"})
     */
    public string $photo = 'geen_foto.jpg';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PoiCategory", inversedBy="pois")
     * @ORM\JoinColumn(name="provincieid", referencedColumnName="provincieid")
     */
    public ?PoiCategory $category = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PoiText", mappedBy="poi")
     */
    public ?PoiText $text = null;
}
