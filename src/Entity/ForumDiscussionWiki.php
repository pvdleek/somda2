<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="somda_forum_discussion_wiki",
 *     indexes={@ORM\Index(name="idx_somda_forum_discussion_wiki__discussionid", columns={"discussionid"})}
 * )
 * @ORM\Entity
 */
class ForumDiscussionWiki
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="wikis")
     * @ORM\JoinColumn(name="discussionid", referencedColumnName="discussionid")
     */
    public ?ForumDiscussion $discussion = null;

    /**
     * @ORM\Column(name="wiki", type="string", length=50, nullable=false)
     */
    public string $wiki = '';

    /**
     * @ORM\Column(name="titel", type="string", length=50, nullable=true)
     */
    public ?string $title = null;
}
