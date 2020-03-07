<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr1TreinTreinnummerlijst
 *
 * @ORM\Table(name="somda_tdr_1_trein_treinnummerlijst")
 * @ORM\Entity
 */
class SomdaTdr1TreinTreinnummerlijst
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
