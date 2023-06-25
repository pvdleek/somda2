<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_jargon")
 * @ORM\Entity
 */
class Jargon
{
    /**
     * @ORM\Column(name="jargonid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="term", type="string", length=15, nullable=false)
     */
    public string $term = '';

    /**
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    public string $image = '';

    /**
     * @ORM\Column(name="description", type="string", length=150, nullable=false)
     */
    public string $description = '';
}
