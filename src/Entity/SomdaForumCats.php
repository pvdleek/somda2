<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumCats
 *
 * @ORM\Table(name="somda_forum_cats")
 * @ORM\Entity
 */
class SomdaForumCats
{
    /**
     * @var int
     *
     * @ORM\Column(name="catid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $catid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $volgorde;


}
