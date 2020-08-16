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
     * @var int|null
     * @ORM\Column(name="discussionid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    public string $title = '';

    /**
     * @var string
     * @ORM\Column(name="viewed", type="bigint", nullable=false)
     */
    public string $viewed = '0';

    /**
     * @var bool
     * @ORM\Column(name="locked", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $locked = false;

    /**
     * @var ForumForum
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumForum", inversedBy="discussions")
     * @ORM\JoinColumn(name="forumid", referencedColumnName="forumid")
     */
    public ForumForum $forum;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    public User $author;

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
