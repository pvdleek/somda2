<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_discussion", indexes={@ORM\Index(name="idx_47915_forumid", columns={"forumid"})})
 * @ORM\Entity(repositoryClass="App\Repository\ForumDiscussion")
 */
class ForumDiscussion
{
    /**
     * @var int
     * @ORM\Column(name="discussionid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="disc_type", type="bigint", nullable=false)
     */
    private $type = 0;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title = '';

    /**
     * @var int
     * @ORM\Column(name="viewed", type="bigint", nullable=false)
     */
    private $viewed = 0;

    /**
     * @var boolean
     * @ORM\Column(name="locked", type="boolean", nullable=false, options={"default"=false})
     */
    private $locked = false;

    /**
     * @var ForumForum
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumForum", inversedBy="discussions")
     * @ORM\JoinColumn(name="forumid", referencedColumnName="forumid")
     */
    private $forum;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    private $author;

    /**
     * @var ForumPost[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPost", mappedBy="discussion")
     */
    private $posts;

    /**
     * @var ForumDiscussionWiki[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumDiscussionWiki", mappedBy="discussion")
     */
    private $wikis;

    /**
     * @var ForumFavorite[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumFavorite", mappedBy="discussion")
     */
    private $favorites;

    /**
     *
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->wikis = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ForumDiscussion
     */
    public function setId(int $id): ForumDiscussion
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return ForumDiscussion
     */
    public function setType(int $type): ForumDiscussion
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ForumDiscussion
     */
    public function setTitle(string $title): ForumDiscussion
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getViewed(): int
    {
        return $this->viewed;
    }

    /**
     * @param int $viewed
     * @return ForumDiscussion
     */
    public function setViewed(int $viewed): ForumDiscussion
    {
        $this->viewed = $viewed;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return ForumDiscussion
     */
    public function setLocked(bool $locked): ForumDiscussion
    {
        $this->locked = $locked;
        return $this;
    }

    /**
     * @return ForumForum
     */
    public function getForum(): ForumForum
    {
        return $this->forum;
    }

    /**
     * @param ForumForum $forum
     * @return ForumDiscussion
     */
    public function setForum(ForumForum $forum): ForumDiscussion
    {
        $this->forum = $forum;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return ForumDiscussion
     */
    public function setAuthor(User $author): ForumDiscussion
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @param ForumPost $forumPost
     * @return ForumDiscussion
     */
    public function addPost(ForumPost $forumPost): ForumDiscussion
    {
        $this->posts[] = $forumPost;
        return $this;
    }

    /**
     * @return ForumPost[]
     */
    public function getPosts(): array
    {
        return $this->posts->toArray();
    }

    /**
     * @param ForumDiscussionWiki $forumDiscussionWiki
     * @return ForumDiscussion
     */
    public function addWiki(ForumDiscussionWiki $forumDiscussionWiki): ForumDiscussion
    {
        $this->wikis[] = $forumDiscussionWiki;
        return $this;
    }

    /**
     * @return ForumDiscussionWiki[]
     */
    public function getWikis(): array
    {
        return $this->wikis->toArray();
    }

    /**
     * @param ForumFavorite $forumFavorite
     * @return ForumDiscussion
     */
    public function addFavorite(ForumFavorite $forumFavorite): ForumDiscussion
    {
        $this->favorites[] = $forumFavorite;
        return $this;
    }

    /**
     * @return ForumFavorite[]
     */
    public function getFavorites(): array
    {
        return $this->favorites->toArray();
    }
}
