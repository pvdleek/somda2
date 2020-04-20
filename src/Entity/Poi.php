<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spot_punt")
 * @ORM\Entity
 */
class Poi extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="puntid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    public $name = '';

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="pois")
     * @ORM\JoinColumn(name="afkid_locatie", referencedColumnName="afkid")
     */
    public $location;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_1", referencedColumnName="afkid")
     */
    public $locationSection1;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_2", referencedColumnName="afkid")
     */
    public $locationSection2;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_dks", referencedColumnName="afkid")
     */
    public $locationPassingRoutes;

    /**
     * @var string|null
     * @ORM\Column(name="kilometrering", type="string", length=25, nullable=true)
     */
    public $kilometre;

    /**
     * @var string|null
     * @ORM\Column(name="gps", type="string", length=25, nullable=true)
     */
    public $gps;

    /**
     * @var string|null
     * @ORM\Column(name="zonstand_winter", type="string", length=50, nullable=true)
     */
    public $sunPositionWinter;

    /**
     * @var string|null
     * @ORM\Column(name="zonstand_zomer", type="string", length=50, nullable=true)
     */
    public $sunPositionSummer;

    /**
     * @var string|null
     * @ORM\Column(name="google_url", type="string", length=200, nullable=true)
     */
    public $googleUrl;

    /**
     * @var string
     * @ORM\Column(name="foto", type="string", length=25, nullable=false, options={"default"="geen_foto.jpg"})
     */
    public $photo = 'geen_foto.jpg';

    /**
     * @var PoiCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\PoiCategory", inversedBy="pois")
     * @ORM\JoinColumn(name="provincieid", referencedColumnName="provincieid")
     */
    public $category;

    /**
     * @var PoiText
     * @ORM\OneToOne(targetEntity="App\Entity\PoiText", mappedBy="poi")
     */
    public $text;
}
