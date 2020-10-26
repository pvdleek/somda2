<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sho_shout", indexes={@ORM\Index(name="IDX_sho_use_id", columns={"sho_use_id"})})
 * @ORM\Entity
 */
class Shout
{
    /**
     * @var int|null
     * @ORM\Column(name="sho_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="sho_ip_address", type="bigint", nullable=false)
     */
    public string $ipAddress;

    /**
     * @var DateTime
     * @ORM\Column(name="sho_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="sho_text", type="string", length=255, nullable=false)
     */
    public string $text;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="sho_use_id", referencedColumnName="use_id")
     */
    public User $author;
}
