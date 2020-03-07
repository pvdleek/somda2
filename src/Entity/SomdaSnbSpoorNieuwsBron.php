<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaSnbSpoorNieuwsBron
 *
 * @ORM\Table(name="somda_snb_spoor_nieuws_bron")
 * @ORM\Entity
 */
class SomdaSnbSpoorNieuwsBron
{
    /**
     * @var int
     *
     * @ORM\Column(name="snb_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $snbId;

    /**
     * @var string
     *
     * @ORM\Column(name="snb_bron", type="string", length=7, nullable=false)
     */
    private $snbBron;

    /**
     * @var string
     *
     * @ORM\Column(name="snb_logo", type="string", length=25, nullable=false)
     */
    private $snbLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="snb_url", type="string", length=30, nullable=false)
     */
    private $snbUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="snb_description", type="string", length=100, nullable=false)
     */
    private $snbDescription;


}
