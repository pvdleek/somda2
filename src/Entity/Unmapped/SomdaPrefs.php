<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaPrefs
 *
 * @ORM\Table(name="somda_prefs", indexes={@ORM\Index(name="idx_48215_sleutel", columns={"sleutel"})})
 * @ORM\Entity
 */
class SomdaPrefs
{
    /**
     * @var int
     *
     * @ORM\Column(name="prefid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $prefid;

    /**
     * @var string
     *
     * @ORM\Column(name="sleutel", type="string", length=25, nullable=false)
     */
    private $sleutel;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private $type = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=90, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="default_value", type="string", length=100, nullable=false)
     */
    private $defaultValue;

    /**
     * @var int
     *
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $volgorde;


}
