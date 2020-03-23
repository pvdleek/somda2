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
    private $block;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=65535, nullable=false)
     */
    private $text = '';

    /**
     * @var string
     * @ORM\Column(name="google_channel", type="string", length=10, nullable=false)
     */
    private $googleChannel = '0';

    /**
     * @var string
     * @ORM\Column(name="ad_code", type="text", length=65535, nullable=false)
     */
    private $adCode;

    /**
     * @return Block
     */
    public function getBlock(): Block
    {
        return $this->block;
    }

    /**
     * @param Block $block
     * @return BlockHelp
     */
    public function setBlock(Block $block): BlockHelp
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return BlockHelp
     */
    public function setText(string $text): BlockHelp
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleChannel(): string
    {
        return $this->googleChannel;
    }

    /**
     * @param string $googleChannel
     * @return BlockHelp
     */
    public function setGoogleChannel(string $googleChannel): BlockHelp
    {
        $this->googleChannel = $googleChannel;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdCode(): string
    {
        return $this->adCode;
    }

    /**
     * @param string $adCode
     * @return BlockHelp
     */
    public function setAdCode(string $adCode): BlockHelp
    {
        $this->adCode = $adCode;
        return $this;
    }
}
