<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaHelpText
 *
 * @ORM\Table(name="somda_help_text")
 * @ORM\Entity
 */
class SomdaHelpText
{
    /**
     * @var int
     *
     * @ORM\Column(name="blokid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $blokid;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="google_channel", type="string", length=10, nullable=false)
     */
    private $googleChannel = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ad_code", type="text", length=0, nullable=false)
     */
    private $adCode;


}
