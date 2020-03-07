<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaPositie
 *
 * @ORM\Table(name="somda_positie")
 * @ORM\Entity
 */
class SomdaPositie
{
    /**
     * @var int
     *
     * @ORM\Column(name="posid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $posid;

    /**
     * @var string
     *
     * @ORM\Column(name="positie", type="string", length=2, nullable=false)
     */
    private $positie;


}
