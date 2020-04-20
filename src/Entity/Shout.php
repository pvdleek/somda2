<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_sht_shout")
 * @ORM\Entity
 */
class Shout extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="sht_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="sht_ip", type="bigint", nullable=false)
     */
    public $ipAddress;

    /**
     * @var DateTime
     * @ORM\Column(name="sht_datumtijd", type="datetime", nullable=false)
     */
    public $timestamp;

    /**
     * @var string
     * @ORM\Column(name="sht_text", type="string", length=255, nullable=false)
     */
    public $text;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="sht_uid", referencedColumnName="uid")
     */
    public $author;
}
