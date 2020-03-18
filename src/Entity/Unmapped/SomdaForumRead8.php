<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumRead8
 *
 * @ORM\Table(name="somda_forum_read_8", indexes={@ORM\Index(name="somda_forum_read_8_idx_uid", columns={"uid"})})
 * @ORM\Entity
 */
class SomdaForumRead8
{
    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $postid;


}
