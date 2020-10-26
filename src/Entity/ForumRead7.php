<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fr7_forum_read_7", indexes={
 *     @ORM\Index(name="IDX_fr7_use_id", columns={"fr7_use_id"}),
 *     @ORM\Index(name="IDX_fr7_fop_id", columns={"fr7_fop_id"}),
 * })
 * @ORM\Entity
 */
class ForumRead7
{
    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost")
     * @ORM\JoinColumn(name="fr7_fop_id", referencedColumnName="fop_id")
     * @ORM\Id
     */
    public ForumPost $post;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="fr7_use_id", referencedColumnName="use_id")
     * @ORM\Id
     */
    public User $user;
}
