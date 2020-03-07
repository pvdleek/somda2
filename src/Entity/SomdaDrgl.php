<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaDrgl
 *
 * @ORM\Table(name="somda_drgl")
 * @ORM\Entity
 */
class SomdaDrgl
{
    /**
     * @var int
     *
     * @ORM\Column(name="drglid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $drglid;

    /**
     * @var int
     *
     * @ORM\Column(name="werkzaamheden", type="bigint", nullable=false)
     */
    private $werkzaamheden = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="pubdatum", type="datetime", nullable=true)
     */
    private $pubdatum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $datum;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="einddatum", type="date", nullable=true)
     */
    private $einddatum;

    /**
     * @var int
     *
     * @ORM\Column(name="public", type="bigint", nullable=false)
     */
    private $public = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=75, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    private $image = '';

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;


}
