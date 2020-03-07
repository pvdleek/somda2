<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumForums
 *
 * @ORM\Table(name="somda_forum_forums", indexes={@ORM\Index(name="idx_47937_catid", columns={"catid"})})
 * @ORM\Entity
 */
class SomdaForumForums
{
    /**
     * @var int
     *
     * @ORM\Column(name="forumid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $forumid;

    /**
     * @var int
     *
     * @ORM\Column(name="catid", type="bigint", nullable=false)
     */
    private $catid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description = '';

    /**
     * @var int
     *
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $volgorde;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="bigint", nullable=false)
     */
    private $type;


}
