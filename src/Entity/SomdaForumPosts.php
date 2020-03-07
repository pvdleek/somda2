<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumPosts
 *
 * @ORM\Table(name="somda_forum_posts", indexes={@ORM\Index(name="idx_47961_date", columns={"date"}), @ORM\Index(name="idx_47961_authorid", columns={"authorid"}), @ORM\Index(name="idx_47961_discussionid", columns={"discussionid"})})
 * @ORM\Entity
 */
class SomdaForumPosts
{
    /**
     * @var int
     *
     * @ORM\Column(name="postid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $postid;

    /**
     * @var int
     *
     * @ORM\Column(name="authorid", type="bigint", nullable=false)
     */
    private $authorid;

    /**
     * @var int
     *
     * @ORM\Column(name="discussionid", type="bigint", nullable=false)
     */
    private $discussionid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time", nullable=false)
     */
    private $time;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="edit_date", type="date", nullable=true)
     */
    private $editDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="edit_time", type="time", nullable=true)
     */
    private $editTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="edit_uid", type="bigint", nullable=true)
     */
    private $editUid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="edit_reason", type="string", length=50, nullable=true)
     */
    private $editReason;

    /**
     * @var int
     *
     * @ORM\Column(name="sign_on", type="bigint", nullable=false)
     */
    private $signOn = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="wiki_check", type="bigint", nullable=false)
     */
    private $wikiCheck = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="wiki_uid", type="bigint", nullable=true)
     */
    private $wikiUid;


}
