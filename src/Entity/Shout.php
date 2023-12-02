<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_sht_shout")
 * @ORM\Entity
 */
class Shout
{
    /**
     * @ORM\Column(name="sht_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="sht_ip", type="integer", nullable=false, options={"unsigned"=true})
     */
    public string $ipAddress = '';

    /**
     * @ORM\Column(name="sht_datumtijd", type="datetime", nullable=false)
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\Column(name="sht_text", type="string", length=255, nullable=false)
     */
    public string $text = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="sht_uid", referencedColumnName="uid")
     */
    public ?User $author = null;
}
