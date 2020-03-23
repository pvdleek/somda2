<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_discussion_wiki", indexes={@ORM\Index(name="idx_47927_discussionid", columns={"discussionid"})})
 * @ORM\Entity
 */
class ForumDiscussionWiki
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="wikis")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     */
    private $discussion;

    /**
     * @var string
     * @ORM\Column(name="wiki", type="string", length=50, nullable=false)
     */
    private $wiki;

    /**
     * @var string|null
     * @ORM\Column(name="titel", type="string", length=50, nullable=true)
     */
    private $title;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ForumDiscussionWiki
     */
    public function setId(int $id): ForumDiscussionWiki
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ForumDiscussion
     */
    public function getDiscussion(): ForumDiscussion
    {
        return $this->discussion;
    }

    /**
     * @param ForumDiscussion $discussion
     * @return ForumDiscussionWiki
     */
    public function setDiscussion(ForumDiscussion $discussion): ForumDiscussionWiki
    {
        $this->discussion = $discussion;
        return $this;
    }

    /**
     * @return string
     */
    public function getWiki(): string
    {
        return $this->wiki;
    }

    /**
     * @param string $wiki
     * @return ForumDiscussionWiki
     */
    public function setWiki(string $wiki): ForumDiscussionWiki
    {
        $this->wiki = $wiki;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return ForumDiscussionWiki
     */
    public function setTitle(?string $title): ForumDiscussionWiki
    {
        $this->title = $title;
        return $this;
    }
}
