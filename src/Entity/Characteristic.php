<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_karakteristiek", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48102_omschrijving", columns={"naam"})})
 * @ORM\Entity
 */
class Characteristic extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="karakteristiek_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=5, nullable=false)
     */
    public $name = '';

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     */
    public $description;
}
