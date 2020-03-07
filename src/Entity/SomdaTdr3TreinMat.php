<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr3TreinMat
 *
 * @ORM\Table(name="somda_tdr_3_trein_mat")
 * @ORM\Entity
 */
class SomdaTdr3TreinMat
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
     * @ORM\Column(name="posid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $posid;

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
     * @ORM\Column(name="mat_naam_id", type="bigint", nullable=true)
     */
    private $matNaamId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mat_type_id", type="bigint", nullable=true)
     */
    private $matTypeId;

    /**
     * @var int
     *
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    private $spots = '0';


}
