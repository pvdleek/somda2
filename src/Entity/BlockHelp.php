<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blh_block_help")
 * @ORM\Entity
 */
class BlockHelp
{
    /**
     * @var Block
     * @ORM\OneToOne(targetEntity="App\Entity\Block", inversedBy="blockHelp")
     * @ORM\JoinColumn(name="blh_blo_id", referencedColumnName="blo_id")
     * @ORM\Id
     */
    public Block $block;

    /**
     * @var string
     * @ORM\Column(name="blh_text", type="text", length=65535, nullable=false)
     */
    public string $text = '';

    /**
     * @var string
     * @ORM\Column(name="blh_google_channel", type="string", length=10, nullable=false)
     */
    public string $googleChannel = '0';

    /**
     * @var string
     * @ORM\Column(name="blh_ad_code", type="text", length=65535, nullable=false)
     */
    public string $adCode;
}
