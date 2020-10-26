<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fr6_forum_read_6", indexes={
 *     @ORM\Index(name="IDX_fr6_use_id", columns={"fr6_use_id"}),
 *     @ORM\Index(name="IDX_fr6_fop_id", columns={"fr6_fop_id"}),
 * })
 * @ORM\Entity
 */
class ForumRead6
{
    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost")
     * @ORM\JoinColumn(name="fr6_fop_id", referencedColumnName="fop_id")
     * @ORM\Id
     */
    public ForumPost $post;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="fr6_use_id", referencedColumnName="use_id")
     * @ORM\Id
     */
    public User $user;
}
