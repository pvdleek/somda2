<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaDrglRead
 *
 * @ORM\Table(name="somda_drgl_read")
 * @ORM\Entity
 */
class SomdaDrglRead
{
    /**
     * @var int
     *
     * @ORM\Column(name="drglid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $drglid;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;


}
