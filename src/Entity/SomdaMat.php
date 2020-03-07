<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaMat
 *
 * @ORM\Table(name="somda_mat", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48117_nummer", columns={"nummer"})}, indexes={@ORM\Index(name="idx_48117_vervoerder_id", columns={"vervoerder_id"})})
 * @ORM\Entity
 */
class SomdaMat
{
    /**
     * @var int
     *
     * @ORM\Column(name="matid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $matid;

    /**
     * @var string
     *
     * @ORM\Column(name="nummer", type="string", length=20, nullable=false)
     */
    private $nummer = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="naam", type="string", length=35, nullable=true)
     */
    private $naam;

    /**
     * @var int|null
     *
     * @ORM\Column(name="vervoerder_id", type="bigint", nullable=true)
     */
    private $vervoerderId;


}
