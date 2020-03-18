<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaSpotPunt
 *
 * @ORM\Table(name="somda_spot_punt")
 * @ORM\Entity
 */
class SomdaSpotPunt
{
    /**
     * @var int
     *
     * @ORM\Column(name="puntid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $puntid;

    /**
     * @var int
     *
     * @ORM\Column(name="provincieid", type="bigint", nullable=false)
     */
    private $provincieid;

    /**
     * @var string
     *
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    private $naam = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="afkid_traject_1", type="bigint", nullable=true)
     */
    private $afkidTraject1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="afkid_traject_2", type="bigint", nullable=true)
     */
    private $afkidTraject2;

    /**
     * @var int|null
     *
     * @ORM\Column(name="afkid_locatie", type="bigint", nullable=true)
     */
    private $afkidLocatie;

    /**
     * @var int|null
     *
     * @ORM\Column(name="afkid_dks", type="bigint", nullable=true)
     */
    private $afkidDks;

    /**
     * @var string|null
     *
     * @ORM\Column(name="kilometrering", type="string", length=25, nullable=true)
     */
    private $kilometrering;

    /**
     * @var string|null
     *
     * @ORM\Column(name="gps", type="string", length=25, nullable=true)
     */
    private $gps;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zonstand_winter", type="string", length=50, nullable=true)
     */
    private $zonstandWinter;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zonstand_zomer", type="string", length=50, nullable=true)
     */
    private $zonstandZomer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="google_url", type="string", length=200, nullable=true)
     */
    private $googleUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="foto", type="string", length=25, nullable=false, options={"default"="geen_foto.jpg"})
     */
    private $foto = 'geen_foto.jpg';


}
