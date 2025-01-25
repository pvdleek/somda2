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
     * @ORM\OneToOne(targetEntity="App\Entity\Block", inversedBy="blockHelp")
     * @ORM\JoinColumn(name="blokid", referencedColumnName="blokid")
     * @ORM\Id
     */
    public ?Block $block = null;

    /**
     * @ORM\Column(name="text", type="text", length=65535, nullable=false)
     */
    public string $text = '';

    /**
     * @ORM\Column(name="ad_code", type="text", length=65535, nullable=false)
     */
    public string $adCode = '';
}
