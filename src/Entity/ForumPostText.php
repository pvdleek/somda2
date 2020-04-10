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
    private $post;

    /**
     * @var boolean
     * @ORM\Column(name="new_style", type="boolean", options={"default"=true})
     */
    private $newStyle = true;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     */
    private $text;

    /**
     * @return ForumPost
     */
    public function getPost(): ForumPost
    {
        return $this->post;
    }

    /**
     * @param ForumPost $post
     * @return ForumPostText
     */
    public function setPost(ForumPost $post): ForumPostText
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNewStyle(): bool
    {
        return $this->newStyle;
    }

    /**
     * @param bool $newStyle
     * @return ForumPostText
     */
    public function setNewStyle(bool $newStyle): ForumPostText
    {
        $this->newStyle = $newStyle;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return ForumPostText
     */
    public function setText(string $text): ForumPostText
    {
        $this->text = $text;
        return $this;
    }
}
