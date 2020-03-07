<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaRijdagen
 *
 * @ORM\Table(name="somda_rijdagen")
 * @ORM\Entity
 */
class SomdaRijdagen
{
    /**
     * @var int
     *
     * @ORM\Column(name="rijdagenid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $rijdagenid;

    /**
     * @var string
     *
     * @ORM\Column(name="rijdagen", type="string", length=11, nullable=false)
     */
    private $rijdagen = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="ma", type="bigint", nullable=true)
     */
    private $ma;

    /**
     * @var int|null
     *
     * @ORM\Column(name="di", type="bigint", nullable=true)
     */
    private $di;

    /**
     * @var int|null
     *
     * @ORM\Column(name="wo", type="bigint", nullable=true)
     */
    private $wo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="do", type="bigint", nullable=true)
     */
    private $do;

    /**
     * @var int|null
     *
     * @ORM\Column(name="vr", type="bigint", nullable=true)
     */
    private $vr;

    /**
     * @var int|null
     *
     * @ORM\Column(name="za", type="bigint", nullable=true)
     */
    private $za;

    /**
     * @var int|null
     *
     * @ORM\Column(name="zf", type="bigint", nullable=true)
     */
    private $zf;


}
