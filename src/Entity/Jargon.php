<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="jar_jargon")
 * @ORM\Entity
 */
class Jargon
{
    /**
     * @var int|null
     * @ORM\Column(name="jar_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="jar_term", type="string", length=15, nullable=false)
     */
    public string $term = '';

    /**
     * @var string
     * @ORM\Column(name="jar_image", type="string", length=20, nullable=false)
     */
    public string $image = '';

    /**
     * @var string
     * @ORM\Column(name="jar_description", type="string", length=150, nullable=false)
     */
    public string $description = '';
}
