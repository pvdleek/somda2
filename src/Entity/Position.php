<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_positie")
 * @ORM\Entity(repositoryClass="App\Repository\Position")
 */
class Position extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="posid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="positie", type="string", length=2, nullable=false)
     */
    public string $name;
}
