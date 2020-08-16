<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="somda_forum_posts_text")
 * @ORM\Entity
 */
class ForumPostText
{
    /**
     * @var ForumPost
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPost", inversedBy="text")
     * @ORM\JoinColumn(name="postid", referencedColumnName="postid")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public ForumPost $post;

    /**
     * @var bool
     * @ORM\Column(name="new_style", type="boolean", options={"default"=true})
     * @JMS\Expose()
     * @SWG\Property(description="Whether the post was created using the new WYSIWYG editor", type="boolean")
     */
    public bool $newStyle = true;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", length=0, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Text of the post", type="text")
     */
    public string $text;
}
