<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="somda_forum_posts",
 *     indexes={
 *         @ORM\Index(name="idx_somda_forum_posts__timestamp", columns={"timestamp"}),
 *         @ORM\Index(name="idx_somda_forum_posts__authorid", columns={"authorid"}),
 *         @ORM\Index(name="idx_somda_forum_posts__discussionid", columns={"discussionid"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ForumPost")
 */
class ForumPost
{
    public const WIKI_CHECK_NOT_CHECKED = 0;
    public const WIKI_CHECK_OK = 1;
    public const WIKI_CHECK_N_A = 2;
    public const WIKI_CHECK_VALUES = [self::WIKI_CHECK_NOT_CHECKED, self::WIKI_CHECK_OK, self::WIKI_CHECK_N_A];

    /**
     * @ORM\Column(name="postid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="authorid", referencedColumnName="uid")
     * @JMS\Expose()
     * @OA\Property(description="The author of the post", ref=@Model(type=User::class))
     */
    public ?User $author = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="posts")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     * @JMS\Exclude()
     */
    public ?ForumDiscussion $discussion = null;

    /**
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the post (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPostText", mappedBy="post")
     * @JMS\Expose()
     * @OA\Property(description="The text of the post", ref=@Model(type=ForumPostText::class))
     */
    public ?ForumPostText $text = null;

    /**
     * @ORM\Column(name="edit_timestamp", type="datetime", nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="ISO-8601 timestamp of the post edit (Y-m-dTH:i:sP)", type="string")
     */
    public ?\DateTime $editTimestamp = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="edit_uid", referencedColumnName="uid")
     * @JMS\Expose()
     * @OA\Property(description="The user that edited the post", ref=@Model(type=User::class))
     */
    public ?User $editor = null;

    /**
     * @ORM\Column(name="edit_reason", type="string", length=50, nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="Reason for editing the post", maxLength=50, type="string")
     */
    public ?string $editReason = null;

    /**
     * @ORM\Column(name="sign_on", type="boolean", nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Whether the signature of the author is included", type="boolean")
     */
    public bool $signatureOn = false;

    /**
     * @ORM\Column(name="wiki_check", type="smallint", nullable=false, options={"default"=ForumPost::WIKI_CHECK_NOT_CHECKED, "unsigned"=true})
     * @Assert\Choice(choices=ForumPost::WIKI_CHECK_VALUES)
     * @JMS\Exclude()
     */
    public int $wikiCheck = self::WIKI_CHECK_NOT_CHECKED;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="wiki_uid", referencedColumnName="uid")
     * @JMS\Exclude()
     */
    public ?User $wikiChecker = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostAlert", mappedBy="post")
     * @JMS\Exclude()
     */
    private $alerts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostLog", mappedBy="post")
     * @JMS\Exclude()
     */
    private $logs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumSearchList", mappedBy="post")
     * @JMS\Exclude()
     */
    private $searchLists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostFavorite", mappedBy="post")
     */
    private $favorites;

    /**
     *
     */
    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->searchLists = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function addAlert(ForumPostAlert $forumPostAlert): ForumPost
    {
        $this->alerts[] = $forumPostAlert;
        $forumPostAlert->post = $this;
        return $this;
    }

    /**
     * @return ForumPostAlert[]
     */
    public function getAlerts(): array
    {
        return $this->alerts->toArray();
    }

    public function addLog(ForumPostLog $forumPostLog): ForumPost
    {
        $this->logs[] = $forumPostLog;
        $forumPostLog->post = $this;
        return $this;
    }

    /**
     * @return ForumPostLog[]
     */
    public function getLogs(): array
    {
        return $this->logs->toArray();
    }

    public function addSearchList(ForumSearchList $forumSearchList): ForumPost
    {
        $this->searchLists[] = $forumSearchList;
        $forumSearchList->post = $this;
        return $this;
    }

    /**
     * @return ForumSearchList[]
     */
    public function getSearchLists(): array
    {
        return $this->searchLists->toArray();
    }

    public function addFavorite(ForumPostFavorite $forumPostFavorite): ForumPost
    {
        $this->favorites[] = $forumPostFavorite;
        return $this;
    }

    /**
     * @return ForumPostFavorite[]
     */
    public function getFavorites(): array
    {
        return $this->favorites->toArray();
    }

    public function getNumberOfFavorites(): int
    {
        return $this->favorites->count();
    }
}
