<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fop_forum_post", indexes={
 *     @ORM\Index(name="IDX_fop_timestamp", columns={"fop_timestamp"}),
 *     @ORM\Index(name="IDX_fop_author_use_id", columns={"fop_author_use_id"}),
 *     @ORM\Index(name="IDX_fop_fod_id", columns={"fop_fod_id"}),
 *     @ORM\Index(name="IDX_fop_editor_use_id", columns={"fop_editor_use_id"}),
 *     @ORM\Index(name="IDX_fop_wiki_checker_use_id", columns={"fop_wiki_checker_use_id"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ForumPost")
 */
class ForumPost
{
    public const WIKI_CHECK_NOT_CHECKED = 0;
    public const WIKI_CHECK_OK = 1;
    public const WIKI_CHECK_N_A = 2;
    public const WIKI_CHECK_VALUES = [self::WIKI_CHECK_NOT_CHECKED, self::WIKI_CHECK_OK, self::WIKI_CHECK_N_A];

    /**
     * @var int|null
     * @ORM\Column(name="fop_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="fop_author_use_id", referencedColumnName="use_id")
     * @JMS\Expose()
     * @SWG\Property(description="The author of the post", ref=@Model(type=User::class))
     */
    public User $author;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="posts")
     * @ORM\JoinColumn(name="fop_fod_id", referencedColumnName="fod_id")
     * @JMS\Exclude()
     */
    public ForumDiscussion $discussion;

    /**
     * @var DateTime
     * @ORM\Column(name="fop_timestamp", type="datetime", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the post (Y-m-dTH:i:sP)", type="string")
     */
    public DateTime $timestamp;

    /**
     * @var ForumPostText
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPostText", mappedBy="post")
     * @JMS\Expose()
     * @SWG\Property(description="The text of the post", ref=@Model(type=ForumPostText::class))
     */
    public ForumPostText $text;

    /**
     * @var DateTime|null
     * @ORM\Column(name="fop_edit_timestamp", type="datetime", nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="ISO-8601 timestamp of the post edit (Y-m-dTH:i:sP)", type="string")
     */
    public ?DateTime $editTimestamp = null;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="fop_editor_use_id", referencedColumnName="use_id")
     * @JMS\Expose()
     * @SWG\Property(description="The user that edited the post", ref=@Model(type=User::class))
     */
    public ?User $editor = null;

    /**
     * @var string|null
     * @ORM\Column(name="fop_edit_reason", type="string", length=50, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Reason for editing the post", maxLength=50, type="string")
     */
    public ?string $editReason = null;

    /**
     * @var bool
     * @ORM\Column(name="fop_signature_on", type="boolean", nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Whether the signature of the author is included", type="boolean")
     */
    public bool $signatureOn = false;

    /**
     * @var integer
     * @ORM\Column(name="wiki_check", type="integer", nullable=false)
     * @Assert\Choice(choices=ForumPost::WIKI_CHECK_VALUES)
     * @JMS\Exclude()
     */
    public int $wikiCheck = self::WIKI_CHECK_NOT_CHECKED;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="fop_wiki_checker_use_id", referencedColumnName="use_id")
     * @JMS\Exclude()
     */
    public ?User $wikiChecker;

    /**
     * @var ForumPostAlert[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostAlert", mappedBy="post")
     * @JMS\Exclude()
     */
    private $alerts;

    /**
     * @var ForumPostLog[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumPostLog", mappedBy="post")
     * @JMS\Exclude()
     */
    private $logs;

    /**
     * @var ForumSearchList[]
     * @ORM\OneToMany(targetEntity="App\Entity\ForumSearchList", mappedBy="post")
     * @JMS\Exclude()
     */
    private $searchLists;

    /**
     * @var ForumPostFavorite[]
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

    /**
     * @param ForumPostAlert $forumPostAlert
     * @return ForumPost
     */
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

    /**
     * @param ForumPostLog $forumPostLog
     * @return ForumPost
     */
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

    /**
     * @param ForumSearchList $forumSearchList
     * @return ForumPost
     */
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

    /**
     * @param ForumPostFavorite $forumPostFavorite
     * @return ForumPost
     */
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

    /**
     * @return int
     */
    public function getNumberOfFavorites(): int
    {
        return $this->favorites->count();
    }
}
