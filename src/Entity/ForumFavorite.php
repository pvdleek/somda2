<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="somda_forum_favorites")
 * @ORM\Entity
 */
class ForumFavorite
{
    public const ALERTING_OFF = 0;
    public const ALERTING_ON = 1;
    public const ALERTING_SENT = 2;
    public const ALERTING_VALUES = [self::ALERTING_OFF, self::ALERTING_ON, self::ALERTING_SENT];

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="favorites")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     * @ORM\Id
     */
    public ?ForumDiscussion $discussion = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="forumFavorites")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public ?User $user = null;

    /**
     * @ORM\Column(name="alerting", type="integer", nullable=false, options={"default"=ForumFavorite::ALERTING_OFF})
     * @Assert\Choice(choices=ForumFavorite::ALERTING_VALUES)
     */
    public int $alerting = self::ALERTING_OFF;
}
