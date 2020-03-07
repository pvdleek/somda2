<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaJargon
 *
 * @ORM\Table(name="somda_jargon")
 * @ORM\Entity
 */
class SomdaJargon
{
    /**
     * @var int
     *
     * @ORM\Column(name="jargonid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $jargonid;

    /**
     * @var string
     *
     * @ORM\Column(name="term", type="string", length=15, nullable=false)
     */
    private $term = '';

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    private $image = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=false)
     */
    private $description = '';


}
