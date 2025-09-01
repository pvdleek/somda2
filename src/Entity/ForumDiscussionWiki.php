<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_discussion_wiki', indexes: [new ORM\Index(name: 'idx_somda_forum_discussion_wiki__discussionid', columns: ['discussionid'])])]
class ForumDiscussionWiki
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ForumDiscussion::class, inversedBy: 'wikis')]
    #[ORM\JoinColumn(name: 'discussionid', referencedColumnName: 'discussionid')]
    public ?ForumDiscussion $discussion = null;

    #[ORM\Column(length: 50, nullable: false, options: ['default' => ''])]
    public string $wiki = '';

    #[ORM\Column(name: 'titel', length: 50, nullable: true)]
    public ?string $title = null;
}
