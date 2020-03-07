<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaRechten
 *
 * @ORM\Table(name="somda_rechten")
 * @ORM\Entity
 */
class SomdaRechten
{
    /**
     * @var int
     *
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $blokid;

    /**
     * @var int
     *
     * @ORM\Column(name="groupid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $groupid;


}
