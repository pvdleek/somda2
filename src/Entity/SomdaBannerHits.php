<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaBannerHits
 *
 * @ORM\Table(name="somda_banner_hits", indexes={@ORM\Index(name="idx_47823_bannerid", columns={"bannerid"})})
 * @ORM\Entity
 */
class SomdaBannerHits
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="bannerid", type="bigint", nullable=false)
     */
    private $bannerid;

    /**
     * @var int
     *
     * @ORM\Column(name="datumtijd", type="bigint", nullable=false)
     */
    private $datumtijd;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=50, nullable=false)
     */
    private $ip = '';


}
