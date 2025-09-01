<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fpf_forum_post_favorite')]
class ForumPostFavorite
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ForumPost::class, inversedBy: 'favorites')]
    #[ORM\JoinColumn(name: 'postid', referencedColumnName: 'postid')]
    public ?ForumPost $post = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'forum_post_favorites')]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;
}
