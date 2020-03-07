<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaVerk
 *
 * @ORM\Table(name="somda_verk", uniqueConstraints={@ORM\UniqueConstraint(name="idx_49103_afkorting_2", columns={"afkorting", "landid"})}, indexes={@ORM\Index(name="idx_49103_landid", columns={"landid"}), @ORM\Index(name="idx_49103_description", columns={"description"})})
 * @ORM\Entity
 */
class SomdaVerk
{
    /**
     * @var int
     *
     * @ORM\Column(name="afkid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $afkid;

    /**
     * @var string
     *
     * @ORM\Column(name="afkorting", type="string", length=10, nullable=false)
     */
    private $afkorting = '';

    /**
     * @var int
     *
     * @ORM\Column(name="landid", type="bigint", nullable=false)
     */
    private $landid;

    /**
     * @var float|null
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $latitude;

    /**
     * @var float|null
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $longitude;

    /**
     * @var int
     *
     * @ORM\Column(name="hafas_code", type="bigint", nullable=false)
     */
    private $hafasCode;

    /**
     * @var string
     *
     * @ORM\Column(name="hafas_desc", type="string", length=50, nullable=false)
     */
    private $hafasDesc = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="traject", type="string", length=15, nullable=false)
     */
    private $traject = '';

    /**
     * @var int
     *
     * @ORM\Column(name="spot_allowed", type="bigint", nullable=false)
     */
    private $spotAllowed;

    /**
     * @var int|null
     *
     * @ORM\Column(name="route_overstaptijd", type="bigint", nullable=true)
     */
    private $routeOverstaptijd;


}
