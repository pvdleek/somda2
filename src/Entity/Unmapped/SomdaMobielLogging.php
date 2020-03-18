<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaMobielLogging
 *
 * @ORM\Table(name="somda_mobiel_logging")
 * @ORM\Entity
 */
class SomdaMobielLogging
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_agent", type="string", length=255, nullable=true)
     */
    private $userAgent;

    /**
     * @var int|null
     *
     * @ORM\Column(name="datetime", type="bigint", nullable=true)
     */
    private $datetime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="uid", type="bigint", nullable=true)
     */
    private $uid;


}
