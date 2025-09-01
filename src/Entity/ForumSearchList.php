<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_zoeken_lijst')]
class ForumSearchList
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ForumSearchWord::class, inversedBy: 'lists')]
    #[ORM\JoinColumn(name: 'woord_id', referencedColumnName: 'woord_id')]
    public ?ForumSearchWord $word = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'search_lists')]
    #[ORM\JoinColumn(name: 'postid', referencedColumnName: 'postid')]
    public ?ForumPost $post = null;

    #[ORM\Column(name: 'titel', nullable: false, options: ['default' => false])]
    public bool $title = false;
}
