<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fdw_forum_discussion_wiki", indexes={@ORM\Index(name="IDX_fdw_fod_id", columns={"fdw_fod_id"})})
 * @ORM\Entity
 */
class ForumDiscussionWiki
{
    /**
     * @var int|null
     * @ORM\Column(name="fdw_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var ForumDiscussion
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumDiscussion", inversedBy="wikis")
     * @ORM\JoinColumn(name="fdw_fod_id", referencedColumnName="fod_id")
     */
    public ForumDiscussion $discussion;

    /**
     * @var string
     * @ORM\Column(name="fdw_wiki", type="string", length=50, nullable=false)
     */
    public string $wiki;

    /**
     * @var string|null
     * @ORM\Column(name="fdw_title", type="string", length=50, nullable=true)
     */
    public ?string $title;
}
