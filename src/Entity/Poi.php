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
     * @var int
     * @ORM\Column(name="puntid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    private $name = '';

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="pois")
     * @ORM\JoinColumn(name="afkid_locatie", referencedColumnName="afkid")
     */
    private $location;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_1", referencedColumnName="afkid")
     */
    private $locationSection1;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_traject_2", referencedColumnName="afkid")
     */
    private $locationSection2;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="afkid_dks", referencedColumnName="afkid")
     */
    private $locationPassingRoutes;

    /**
     * @var string|null
     * @ORM\Column(name="kilometrering", type="string", length=25, nullable=true)
     */
    private $kilometre;

    /**
     * @var string|null
     * @ORM\Column(name="gps", type="string", length=25, nullable=true)
     */
    private $gps;

    /**
     * @var string|null
     * @ORM\Column(name="zonstand_winter", type="string", length=50, nullable=true)
     */
    private $sunPositionWinter;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zonstand_zomer", type="string", length=50, nullable=true)
     */
    private $sunPositionSummer;

    /**
     * @var string|null
     * @ORM\Column(name="google_url", type="string", length=200, nullable=true)
     */
    private $googleUrl;

    /**
     * @var string
     * @ORM\Column(name="foto", type="string", length=25, nullable=false, options={"default"="geen_foto.jpg"})
     */
    private $photo = 'geen_foto.jpg';

    /**
     * @var PoiCategory
     * @ORM\ManyToOne(targetEntity="App\Entity\PoiCategory", inversedBy="pois")
     * @ORM\JoinColumn(name="provincieid", referencedColumnName="provincieid")
     */
    private $category;

    /**
     * @var PoiText
     * @ORM\OneToOne(targetEntity="App\Entity\PoiText", mappedBy="poi")
     */
    private $text;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Poi
     */
    public function setId(int $id): Poi
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Poi
     */
    public function setName(string $name): Poi
    {
        $this->name = $name;
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
     * @return Poi
     */
    public function setLocation(Location $location): Poi
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocationSection1(): Location
    {
        return $this->locationSection1;
    }

    /**
     * @param Location $locationSection1
     * @return Poi
     */
    public function setLocationSection1(Location $locationSection1): Poi
    {
        $this->locationSection1 = $locationSection1;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocationSection2(): Location
    {
        return $this->locationSection2;
    }

    /**
     * @param Location $locationSection2
     * @return Poi
     */
    public function setLocationSection2(Location $locationSection2): Poi
    {
        $this->locationSection2 = $locationSection2;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocationPassingRoutes(): Location
    {
        return $this->locationPassingRoutes;
    }

    /**
     * @param Location $locationPassingRoutes
     * @return Poi
     */
    public function setLocationPassingRoutes(Location $locationPassingRoutes): Poi
    {
        $this->locationPassingRoutes = $locationPassingRoutes;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKilometre(): ?string
    {
        return $this->kilometre;
    }

    /**
     * @param string|null $kilometre
     * @return Poi
     */
    public function setKilometre(?string $kilometre): Poi
    {
        $this->kilometre = $kilometre;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGps(): ?string
    {
        return $this->gps;
    }

    /**
     * @param string|null $gps
     * @return Poi
     */
    public function setGps(?string $gps): Poi
    {
        $this->gps = $gps;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSunPositionWinter(): ?string
    {
        return $this->sunPositionWinter;
    }

    /**
     * @param string|null $sunPositionWinter
     * @return Poi
     */
    public function setSunPositionWinter(?string $sunPositionWinter): Poi
    {
        $this->sunPositionWinter = $sunPositionWinter;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSunPositionSummer(): ?string
    {
        return $this->sunPositionSummer;
    }

    /**
     * @param string|null $sunPositionSummer
     * @return Poi
     */
    public function setSunPositionSummer(?string $sunPositionSummer): Poi
    {
        $this->sunPositionSummer = $sunPositionSummer;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGoogleUrl(): ?string
    {
        return $this->googleUrl;
    }

    /**
     * @param string|null $googleUrl
     * @return Poi
     */
    public function setGoogleUrl(?string $googleUrl): Poi
    {
        $this->googleUrl = $googleUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return Poi
     */
    public function setPhoto(string $photo): Poi
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return PoiCategory
     */
    public function getCategory(): PoiCategory
    {
        return $this->category;
    }

    /**
     * @param PoiCategory $category
     * @return Poi
     */
    public function setCategory(PoiCategory $category): Poi
    {
        $this->category = $category;
        return $this;
    }
}
