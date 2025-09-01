<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_posts_text')]
class ForumPostText
{
    /**
     * @JMS\Exclude()
     */
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: ForumPost::class, inversedBy: 'text')]
    #[ORM\JoinColumn(name: 'postid', referencedColumnName: 'postid')]
    public ?ForumPost $post = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Whether the post was created using the new WYSIWYG editor", type="boolean")
     */
    #[ORM\Column(name: 'new_style', type: 'boolean', options: ['default' => true])]
    public bool $new_style = true;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Text of the post", type="text")
     */
    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    public string $text = '';
}
