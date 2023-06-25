<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fpf_forum_post_favorite")
 * @ORM\Entity
 */
class ForumPostFavorite
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="favorites")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     */
    public ?ForumPost $post = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="forumPostFavorites")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public ?User $user = null;
}
