<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr9TreinTreinnummerlijst
 *
 * @ORM\Table(name="somda_tdr_9_trein_treinnummerlijst")
 * @ORM\Entity
 */
class SomdaTdr9TreinTreinnummerlijst
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
