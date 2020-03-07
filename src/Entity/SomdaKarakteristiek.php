<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaKarakteristiek
 *
 * @ORM\Table(name="somda_karakteristiek", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48102_omschrijving", columns={"naam"})})
 * @ORM\Entity
 */
class SomdaKarakteristiek
{
    /**
     * @var int
     *
     * @ORM\Column(name="karakteristiek_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $karakteristiekId;

    /**
     * @var string
     *
     * @ORM\Column(name="naam", type="string", length=5, nullable=false)
     */
    private $naam = '';

    /**
     * @var string
     *
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     */
    private $omschrijving;


}
