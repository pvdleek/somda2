<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_log")
 * @ORM\Entity
 */
class ForumPostLog
{
    public const ACTION_POST_NEW = 0;
    public const ACTION_POST_EDIT = 1;
    public const ACTION_POST_DELETE = 2;

    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="logs")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     */
    private $post;

    /**
     * @var int
     * @ORM\Column(name="actie", type="bigint", nullable=false)
     */
    private $action = self::ACTION_POST_NEW;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ForumPostLog
     */
    public function setId(int $id): ForumPostLog
    {
        $this->id = $id;
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
     * @return ForumPostLog
     */
    public function setPost(ForumPost $post): ForumPostLog
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return int
     */
    public function getAction(): int
    {
        return $this->action;
    }

    /**
     * @param int $action
     * @return ForumPostLog
     */
    public function setAction(int $action): ForumPostLog
    {
        $this->action = $action;
        return $this;
    }
}
