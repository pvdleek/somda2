<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr12TreinTreinnummerlijst
 *
 * @ORM\Table(name="somda_tdr_12_trein_treinnummerlijst")
 * @ORM\Entity
 */
class SomdaTdr12TreinTreinnummerlijst
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
     * @ORM\Column(name="treinnummerlijst_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $treinnummerlijstId;


}
