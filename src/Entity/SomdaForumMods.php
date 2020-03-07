<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumMods
 *
 * @ORM\Table(name="somda_forum_mods")
 * @ORM\Entity
 */
class SomdaForumMods
{
    /**
     * @var int
     *
     * @ORM\Column(name="forumid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $forumid;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;


}
