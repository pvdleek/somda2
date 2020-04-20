<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_logging")
 * @ORM\Entity
 */
class Log extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="logid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var DateTime
     * @ORM\Column(name="datumtijd", type="datetime", nullable=false)
     */
    public $timestamp;

    /**
     * @var int
     * @ORM\Column(name="ip", type="bigint", nullable=false)
     */
    public $ipAddress;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    public $user;

    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(name="blokid", referencedColumnName="blokid")
     */
    public $block;
}
