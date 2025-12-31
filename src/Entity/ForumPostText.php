<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_posts_text')]
class ForumPostText
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: ForumPost::class, inversedBy: 'text')]
    #[ORM\JoinColumn(name: 'postid', referencedColumnName: 'postid')]
    public ?ForumPost $post = null;

    #[ORM\Column(name: 'new_style', type: 'boolean', options: ['default' => true])]
    public bool $new_style = true;

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    public string $text = '';
}
