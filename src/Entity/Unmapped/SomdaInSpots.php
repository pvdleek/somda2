<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaInSpots
 *
 * @ORM\Table(name="somda_in_spots", indexes={@ORM\Index(name="idx_48063_uid", columns={"uid"}), @ORM\Index(name="idx_48063_mat", columns={"mat"})})
 * @ORM\Entity
 */
class SomdaInSpots
{
    /**
     * @var int
     *
     * @ORM\Column(name="spotid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $spotid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="spotstabel_id", type="bigint", nullable=true)
     */
    private $spotstabelId;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="string", length=255, nullable=false)
     */
    private $extra = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $datum;

    /**
     * @var int
     *
     * @ORM\Column(name="dagenverschil", type="bigint", nullable=false)
     */
    private $dagenverschil = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="dag", type="bigint", nullable=false)
     */
    private $dag;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tijd", type="bigint", nullable=true)
     */
    private $tijd;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="locatie", type="string", length=15, nullable=false)
     */
    private $locatie = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="locatieid", type="bigint", nullable=true)
     */
    private $locatieid;

    /**
     * @var string
     *
     * @ORM\Column(name="mat", type="string", length=20, nullable=false)
     */
    private $mat = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="matid", type="bigint", nullable=true)
     */
    private $matid;

    /**
     * @var string
     *
     * @ORM\Column(name="treinnr", type="string", length=15, nullable=false)
     */
    private $treinnr = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="treinid", type="bigint", nullable=true)
     */
    private $treinid;

    /**
     * @var string
     *
     * @ORM\Column(name="positie", type="string", length=2, nullable=false)
     */
    private $positie;

    /**
     * @var int|null
     *
     * @ORM\Column(name="posid", type="bigint", nullable=true)
     */
    private $posid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dgrid", type="bigint", nullable=true)
     */
    private $dgrid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dgrnr", type="bigint", nullable=true)
     */
    private $dgrnr;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dgrid_nu", type="bigint", nullable=true)
     */
    private $dgridNu;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dgrnr_nu", type="bigint", nullable=true)
     */
    private $dgrnrNu;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="mat_datum", type="date", nullable=true)
     */
    private $matDatum;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mat_tijd", type="bigint", nullable=true)
     */
    private $matTijd;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mat_dgrid", type="bigint", nullable=true)
     */
    private $matDgrid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mat_dgrnr", type="bigint", nullable=true)
     */
    private $matDgrnr;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dienst_datum", type="date", nullable=true)
     */
    private $dienstDatum;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dienst_tijd", type="bigint", nullable=true)
     */
    private $dienstTijd;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dienst_matid", type="bigint", nullable=true)
     */
    private $dienstMatid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="actie", type="string", length=100, nullable=true)
     */
    private $actie;

    /**
     * @var int
     *
     * @ORM\Column(name="spot_continue", type="bigint", nullable=false, options={"default"="1"})
     */
    private $spotContinue = '1';


}
