<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spot_punt")
 * @ORM\Entity
 */
class Poi
{
    /**
     * @var int|null
     * @ORM\Column(name="puntid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    public string $name = '';

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="pois")
     * @ORM\JoinColumn(name="afkid_locatie", referencedColumnName="afkid")
     */
    public Location $location;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_1", referencedColumnName="afkid")
     */
    public Location $locationSection1;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_2", referencedColumnName="afkid")
     */
    public Location $locationSection2;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_dks", referencedColumnName="afkid")
     */
    public Location $locationForRoutes;

    /**
     * @var string|null
     * @ORM\Column(name="kilometrering", type="string", length=25, nullable=true)
     */
    public ?string $kilometre;

    /**
     * @var string|null
     * @ORM\Column(name="gps", type="string", length=25, nullable=true)
     */
    public ?string $gps;

    /**
     * @var string|null
     * @ORM\Column(name="zonstand_winter", type="string", length=50, nullable=true)
     */
    public ?string $sunPositionWinter;

    /**
     * @var string|null
     * @ORM\Column(name="zonstand_zomer", type="string", length=50, nullable=true)
     */
    public ?string $sunPositionSummer;

    /**
     * @var string|null
     * @ORM\Column(name="google_url", type="string", length=200, nullable=true)
     */
    public ?string $googleUrl;

    /**
     * @var string
     * @ORM\Column(name="foto", type="string", length=25, nullable=false, options={"default"="geen_foto.jpg"})
     */
    public string $photo = 'geen_foto.jpg';

    /**
     * @var PoiCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\PoiCategory", inversedBy="pois")
     * @ORM\JoinColumn(name="provincieid", referencedColumnName="provincieid")
     */
    public PoiCategory $category;

    /**
     * @var PoiText
     * @ORM\OneToOne(targetEntity="App\Entity\PoiText", mappedBy="poi")
     */
    public PoiText $text;
}
