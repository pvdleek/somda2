<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fpl_forum_post_log", indexes={@ORM\Index(name="IDX_fpl_fop_id", columns={"fpl_fop_id"})})
 * @ORM\Entity
 */
class ForumPostLog
{
    public const ACTION_POST_NEW = 0;
    public const ACTION_POST_EDIT = 1;
    public const ACTION_VALUES = [self::ACTION_POST_NEW, self::ACTION_POST_EDIT];

    /**
     * @var int|null
     * @ORM\Column(name="fpl_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="logs")
     * @ORM\JoinColumn(name="fpl_fop_id", referencedColumnName="fop_id")
     */
    public ForumPost $post;

    /**
     * @var int
     * @ORM\Column(name="fpl_action", type="integer", nullable=false, options={"default"=ForumPostLog::ACTION_POST_NEW})
     * @Assert\Choice(choices=ForumPostLog::ACTION_VALUES)
     */
    public int $action = self::ACTION_POST_NEW;
}
