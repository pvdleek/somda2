<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaMatChanges
 *
 * @ORM\Table(name="somda_mat_changes")
 * @ORM\Entity
 */
class SomdaMatChanges
{
    /**
     * @var int
     *
     * @ORM\Column(name="matsmsid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $matsmsid;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $datum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak1", type="string", length=15, nullable=true)
     */
    private $bak1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak2", type="string", length=15, nullable=true)
     */
    private $bak2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak3", type="string", length=15, nullable=true)
     */
    private $bak3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak4", type="string", length=15, nullable=true)
     */
    private $bak4;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak5", type="string", length=15, nullable=true)
     */
    private $bak5;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak6", type="string", length=15, nullable=true)
     */
    private $bak6;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak7", type="string", length=15, nullable=true)
     */
    private $bak7;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak8", type="string", length=15, nullable=true)
     */
    private $bak8;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak9", type="string", length=15, nullable=true)
     */
    private $bak9;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak10", type="string", length=15, nullable=true)
     */
    private $bak10;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak11", type="string", length=15, nullable=true)
     */
    private $bak11;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak12", type="string", length=15, nullable=true)
     */
    private $bak12;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bak13", type="string", length=15, nullable=true)
     */
    private $bak13;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opmerkingen", type="string", length=255, nullable=true)
     */
    private $opmerkingen;


}
