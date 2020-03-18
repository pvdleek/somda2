<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumRead7
 *
 * @ORM\Table(name="somda_forum_read_7", indexes={@ORM\Index(name="somda_forum_read_7_idx_uid", columns={"uid"})})
 * @ORM\Entity
 */
class SomdaForumRead7
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
