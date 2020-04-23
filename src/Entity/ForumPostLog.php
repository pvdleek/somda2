<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="somda_forum_log")
 * @ORM\Entity
 */
class ForumPostLog extends Entity
{
    public const ACTION_POST_NEW = 0;
    public const ACTION_POST_EDIT = 1;
    public const ACTION_POST_DELETE = 2;
    public const ACTION_VALUES = [self::ACTION_POST_NEW, self::ACTION_POST_EDIT, self::ACTION_POST_DELETE];

    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="logs")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     */
    public ForumPost $post;

    /**
     * @var int
     * @ORM\Column(name="actie", type="bigint", nullable=false)
     * @Assert\Choice(choices=ForumPostLog::ACTION_VALUES)
     */
    public int $action = self::ACTION_POST_NEW;
}
