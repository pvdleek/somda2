<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="somda_forum_discussion_wiki",
 *     indexes={@ORM\Index(name="idx_47927_discussionid", columns={"discussionid"})}
 * )
 * @ORM\Entity
 */
class ForumDiscussionWiki
{
    /**
     * @var int|null
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="wikis")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     */
    public ForumDiscussion $discussion;

    /**
     * @var string
     * @ORM\Column(name="wiki", type="string", length=50, nullable=false)
     */
    public string $wiki;

    /**
     * @var string|null
     * @ORM\Column(name="titel", type="string", length=50, nullable=true)
     */
    public ?string $title;
}
