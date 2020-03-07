<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaTdr6SE
 *
 * @ORM\Table(name="somda_tdr_6_s_e")
 * @ORM\Entity
 */
class SomdaTdr6SE
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
     * @var int
     *
     * @ORM\Column(name="v_locatieid", type="bigint", nullable=false)
     */
    private $vLocatieid;

    /**
     * @var string
     *
     * @ORM\Column(name="v_actie", type="string", length=1, nullable=false, options={"default"="-"})
     */
    private $vActie = '-';

    /**
     * @var int
     *
     * @ORM\Column(name="v_tijd", type="bigint", nullable=false)
     */
    private $vTijd;

    /**
     * @var int
     *
     * @ORM\Column(name="a_locatieid", type="bigint", nullable=false)
     */
    private $aLocatieid;

    /**
     * @var string
     *
     * @ORM\Column(name="a_actie", type="string", length=1, nullable=false, options={"default"="-"})
     */
    private $aActie = '-';

    /**
     * @var int
     *
     * @ORM\Column(name="a_tijd", type="bigint", nullable=false)
     */
    private $aTijd;


}
