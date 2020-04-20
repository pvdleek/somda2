<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_read_9", indexes={@ORM\Index(name="somda_forum_read_9_idx_uid", columns={"uid"})})
 * @ORM\Entity
 */
class ForumRead9
{
    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     */
    public $post;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public $user;
}
