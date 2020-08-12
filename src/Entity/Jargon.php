<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_jargon")
 * @ORM\Entity
 */
class Jargon extends Entity
{
    /**
     * @var int|null
     * @ORM\Column(name="jargonid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="term", type="string", length=15, nullable=false)
     */
    public string $term = '';

    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    public string $image = '';

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=150, nullable=false)
     */
    public string $description = '';
}
