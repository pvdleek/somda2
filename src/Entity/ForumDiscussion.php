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
     * @ORM\Column(name="discussionid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="title", type="string", length=75, nullable=false)
     */
    public string $title = '';

    /**
     * @ORM\Column(name="viewed", type="integer", nullable=false, options={"default"=0, "unsigned"=true})
     */
    public int $viewed = 0;

    /**
     * @ORM\Column(name="locked", type="boolean", nullable=false, options={"default"=false})
     */
    public bool $locked = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumForum", inversedBy="discussions")
     * @ORM\JoinColumn(name="forumid", referencedColumnName="forumid")
     */
    public ?ForumForum $forum = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     */
    public ?User $author = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPost", mappedBy="discussion")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumDiscussionWiki", mappedBy="discussion")
     */
    private $wikis;

    /**
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
