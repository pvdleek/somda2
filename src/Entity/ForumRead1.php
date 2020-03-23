<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_read_1", indexes={@ORM\Index(name="somda_forum_read_1_idx_uid", columns={"uid"})})
 * @ORM\Entity
 */
class ForumRead1
{
    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     */
    private $post;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    private $user;

    /**
     * @return ForumPost
     */
    public function getPost(): ForumPost
    {
        return $this->post;
    }

    /**
     * @param ForumPost $post
     * @return ForumRead1
     */
    public function setPost(ForumPost $post): ForumRead1
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return ForumRead1
     */
    public function setUser(User $user): ForumRead1
    {
        $this->user = $user;
        return $this;
    }
}
