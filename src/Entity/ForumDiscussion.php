<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ForumDiscussionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumDiscussionRepository::class)]
#[ORM\Table(name: 'somda_forum_discussion')]
#[ORM\Index(name: 'idx_somda_forum_discussion__forumid', columns: ['forumid'])]
class ForumDiscussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'discussionid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(length: 75, nullable: false, options: ['default' => ''])]
    public string $title = '';

    #[ORM\Column(nullable: false, options: ['default' => 0, 'unsigned' => true])]
    public int $viewed = 0;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $locked = false;

    #[ORM\ManyToOne(targetEntity: ForumForum::class, inversedBy: 'discussions')]
    #[ORM\JoinColumn(name: 'forumid', referencedColumnName: 'forumid')]
    public ?ForumForum $forum = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'authorid', referencedColumnName: 'uid')]
    public ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: ForumPost::class)]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: ForumDiscussionWiki::class)]
    private Collection $wikis;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: ForumFavorite::class)]
    private Collection $favorites;

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
