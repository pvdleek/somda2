<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="foa_forum_favorite", indexes={
 *     @ORM\Index(name="IDX_foa_fod_id", columns={"foa_fod_id"}),
 *     @ORM\Index(name="IDX_foa_use_id", columns={"foa_use_id"})
 * })
 * @ORM\Entity
 */
class ForumFavorite
{
    public const ALERTING_OFF = 0;
    public const ALERTING_ON = 1;
    public const ALERTING_SENT = 2;
    public const ALERTING_VALUES = [self::ALERTING_OFF, self::ALERTING_ON, self::ALERTING_SENT];

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="favorites")
     * @ORM\JoinColumn(name="foa_fod_id", referencedColumnName="fod_id")
     * @ORM\Id
     */
    public ForumDiscussion $discussion;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="forumFavorites")
     * @ORM\JoinColumn(name="foa_use_id", referencedColumnName="use_id")
     * @ORM\Id
     */
    public User $user;

    /**
     * @var int
     * @ORM\Column(name="foa_alerting", type="integer", nullable=false, options={"default"=ForumFavorite::ALERTING_OFF})
     * @Assert\Choice(choices=ForumFavorite::ALERTING_VALUES)
     */
    public int $alerting = self::ALERTING_OFF;
}
