<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_forum_favorites")
 * @ORM\Entity
 */
class ForumFavorite
{
    public const ALERTING_OFF = 0;
    public const ALERTING_ON = 1;
    public const ALERTING_SENT = 2;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="favorites")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     * @ORM\Id
     */
    private $discussion;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="forumFavorites")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="alerting", type="bigint", nullable=false)
     */
    private $alerting = self::ALERTING_OFF;

    /**
     * @return ForumDiscussion
     */
    public function getDiscussion(): ForumDiscussion
    {
        return $this->discussion;
    }

    /**
     * @param ForumDiscussion $discussion
     * @return ForumFavorite
     */
    public function setDiscussion(ForumDiscussion $discussion): ForumFavorite
    {
        $this->discussion = $discussion;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return ForumFavorite
     */
    public function setUser(User $user): ForumFavorite
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getAlerting(): int
    {
        return $this->alerting;
    }

    /**
     * @param int $alerting
     * @return ForumFavorite
     */
    public function setAlerting(int $alerting): ForumFavorite
    {
        $this->alerting = $alerting;
        return $this;
    }
}
