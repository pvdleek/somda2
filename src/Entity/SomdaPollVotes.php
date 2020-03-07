<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaPollVotes
 *
 * @ORM\Table(name="somda_poll_votes")
 * @ORM\Entity
 */
class SomdaPollVotes
{
    /**
     * @var int
     *
     * @ORM\Column(name="pollid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pollid;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="vote", type="bigint", nullable=false)
     */
    private $vote;


}
