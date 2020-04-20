<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_posts_text")
 * @ORM\Entity
 */
class ForumPostText
{
    /**
     * @var ForumPost
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPost", inversedBy="text")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     */
    public $post;

    /**
     * @var boolean
     * @ORM\Column(name="new_style", type="boolean", options={"default"=true})
     */
    public $newStyle = true;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    public $text;
}
