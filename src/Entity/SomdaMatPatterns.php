<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaMatPatterns
 *
 * @ORM\Table(name="somda_mat_patterns", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48139_volgorde", columns={"volgorde"})})
 * @ORM\Entity
 */
class SomdaMatPatterns
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $volgorde;

    /**
     * @var string
     *
     * @ORM\Column(name="pattern", type="string", length=80, nullable=false)
     */
    private $pattern;

    /**
     * @var string
     *
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    private $naam;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tekening", type="string", length=30, nullable=true)
     */
    private $tekening;


}
