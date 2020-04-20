<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_help_text")
 * @ORM\Entity
 */
class BlockHelp
{
    /**
     * @var Block
     * @ORM\OneToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(name="blokid", referencedColumnName="blokid")
     * @ORM\Id
     */
    public $block;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=65535, nullable=false)
     */
    public $text = '';

    /**
     * @var string
     * @ORM\Column(name="google_channel", type="string", length=10, nullable=false)
     */
    public $googleChannel = '0';

    /**
     * @var string
     * @ORM\Column(name="ad_code", type="text", length=65535, nullable=false)
     */
    public $adCode;
}
