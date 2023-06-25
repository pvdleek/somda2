<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="somda_mat_patterns",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_48139_volgorde", columns={"volgorde"})}
 * )
 * @ORM\Entity
 */
class TrainNamePattern
{
    /**
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="volgorde", type="integer", nullable=false, options={"default"="1"})
     */
    public int $order = 1;

    /**
     * @ORM\Column(name="pattern", type="string", length=80, nullable=false, options={"default"=""})
     */
    public string $pattern = '';

    /**
     * @ORM\Column(name="naam", type="string", length=50, nullable=false, options={"default"=""})
     */
    public string $name = '';

    /**
     * @ORM\Column(name="tekening", type="string", length=30, nullable=true)
     */
    public ?string $image = null;
}
