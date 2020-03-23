<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_zoeken_lijst")
 * @ORM\Entity
 */
class ForumSearchList
{
    /**
     * @var ForumSearchWord
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumSearchWord", inversedBy="lists")
     * @ORM\JoinColumn(name="woord_id", referencedColumnName="woord_id")
     * @ORM\Id
     */
    private $word;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="searchLists")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     */
    private $post;

    /**
     * @var boolean
     * @ORM\Column(name="titel", type="boolean", nullable=false)
     */
    private $title = false;

    /**
     * @return ForumSearchWord
     */
    public function getWord(): ForumSearchWord
    {
        return $this->word;
    }

    /**
     * @param ForumSearchWord $word
     * @return ForumSearchList
     */
    public function setWord(ForumSearchWord $word): ForumSearchList
    {
        $this->word = $word;
        return $this;
    }

    /**
     * @return ForumPost
     */
    public function getPost(): ForumPost
    {
        return $this->post;
    }

    /**
     * @param ForumPost $post
     * @return ForumSearchList
     */
    public function setPost(ForumPost $post): ForumSearchList
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTitle(): bool
    {
        return $this->title;
    }

    /**
     * @param bool $title
     * @return ForumSearchList
     */
    public function setTitle(bool $title): ForumSearchList
    {
        $this->title = $title;
        return $this;
    }
}
