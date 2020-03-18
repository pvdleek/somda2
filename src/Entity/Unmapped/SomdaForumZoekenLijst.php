<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumZoekenLijst
 *
 * @ORM\Table(name="somda_forum_zoeken_lijst")
 * @ORM\Entity
 */
class SomdaForumZoekenLijst
{
    /**
     * @var int
     *
     * @ORM\Column(name="woord_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $woordId;

    /**
     * @var int
     *
     * @ORM\Column(name="post_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $postId;

    /**
     * @var int
     *
     * @ORM\Column(name="titel", type="bigint", nullable=false)
     */
    private $titel = '0';


}
