<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="fpt_forum_post_text")
 * @ORM\Entity
 */
class ForumPostText
{
    /**
     * @var ForumPost
     * @ORM\OneToOne(targetEntity="App\Entity\ForumPost", inversedBy="text")
     * @ORM\JoinColumn(name="fpt_fop_id", referencedColumnName="fop_id")
     * @ORM\Id
     * @JMS\Exclude()
     */
    public ForumPost $post;

    /**
     * @var bool
     * @ORM\Column(name="fpt_new_style", type="boolean", options={"default"=true})
     * @JMS\Expose()
     * @SWG\Property(description="Whether the post was created using the new WYSIWYG editor", type="boolean")
     */
    public bool $newStyle = true;

    /**
     * @var string
     * @ORM\Column(name="fpt_text", type="text", length=0, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Text of the post", type="text")
     */
    public string $text;
}
