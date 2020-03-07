<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr9InSE
 *
 * @ORM\Table(name="somda_tdr_9_in_s_e")
 * @ORM\Entity
 */
class SomdaTdr9InSE
{
    /**
     * @var int
     *
     * @ORM\Column(name="treinid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $treinid;

    /**
     * @var int
     *
     * @ORM\Column(name="dag", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $dag;

    /**
     * @var int|null
     *
     * @ORM\Column(name="min", type="bigint", nullable=true)
     */
    private $min;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max", type="bigint", nullable=true)
     */
    private $max;


}
