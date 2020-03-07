<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumDiscussion
 *
 * @ORM\Table(name="somda_forum_discussion", indexes={@ORM\Index(name="idx_47915_forumid", columns={"forumid"})})
 * @ORM\Entity
 */
class SomdaForumDiscussion
{
    /**
     * @var int
     *
     * @ORM\Column(name="discussionid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $discussionid;

    /**
     * @var int
     *
     * @ORM\Column(name="forumid", type="bigint", nullable=false)
     */
    private $forumid;

    /**
     * @var int
     *
     * @ORM\Column(name="disc_type", type="bigint", nullable=false)
     */
    private $discType = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title = '';

    /**
     * @var int
     *
     * @ORM\Column(name="authorid", type="bigint", nullable=false)
     */
    private $authorid;

    /**
     * @var int
     *
     * @ORM\Column(name="viewed", type="bigint", nullable=false)
     */
    private $viewed = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="locked", type="bigint", nullable=false)
     */
    private $locked = '0';


}
