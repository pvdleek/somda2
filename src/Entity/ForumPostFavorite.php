<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fpf_forum_post_favorite", indexes={
 *     @ORM\Index(name="IDX_fpf_fop_id", columns={"fpf_fop_id"}),
 *     @ORM\Index(name="IDX_fpf_use_id", columns={"fpf_use_id"})
 * })
 * @ORM\Entity
 */
class ForumPostFavorite
{
    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="favorites")
     * @ORM\JoinColumn(name="fpf_fop_id", referencedColumnName="fop_id")
     * @ORM\Id
     */
    public ForumPost $post;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="forumPostFavorites")
     * @ORM\JoinColumn(name="fpf_use_id", referencedColumnName="use_id")
     * @ORM\Id
     */
    public User $user;
}
