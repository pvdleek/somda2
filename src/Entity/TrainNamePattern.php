<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_patterns", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48139_volgorde", columns={"volgorde"})})
 * @ORM\Entity
 */
class TrainNamePattern extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    public int $order;

    /**
     * @var string
     * @ORM\Column(name="pattern", type="string", length=80, nullable=false)
     */
    public string $pattern;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    public string $name;

    /**
     * @var string|null
     * @ORM\Column(name="tekening", type="string", length=30, nullable=true)
     */
    public ?string $image;
}
