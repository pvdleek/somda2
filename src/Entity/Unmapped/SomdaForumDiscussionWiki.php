<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumDiscussionWiki
 *
 * @ORM\Table(name="somda_forum_discussion_wiki", indexes={@ORM\Index(name="idx_47927_discussionid", columns={"discussionid"})})
 * @ORM\Entity
 */
class SomdaForumDiscussionWiki
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="discussionid", type="bigint", nullable=false)
     */
    private $discussionid;

    /**
     * @var string
     *
     * @ORM\Column(name="wiki", type="string", length=50, nullable=false)
     */
    private $wiki;

    /**
     * @var string|null
     *
     * @ORM\Column(name="titel", type="string", length=50, nullable=true)
     */
    private $titel;


}
