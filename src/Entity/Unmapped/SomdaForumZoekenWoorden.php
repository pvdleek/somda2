<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaForumZoekenWoorden
 *
 * @ORM\Table(name="somda_forum_zoeken_woorden", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48035_woord", columns={"woord"})})
 * @ORM\Entity
 */
class SomdaForumZoekenWoorden
{
    /**
     * @var int
     *
     * @ORM\Column(name="woord_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $woordId;

    /**
     * @var string
     *
     * @ORM\Column(name="woord", type="string", length=20, nullable=false)
     */
    private $woord;


}
