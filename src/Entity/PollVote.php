<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_poll_votes")
 * @ORM\Entity
 */
class PollVote
{
    /**
     * @var Poll
     * @ORM\ManyToOne(targetEntity="App\Entity\Poll", inversedBy="votes")
     * @ORM\JoinColumn(name="pollid", referencedColumnName="pollid")
     * @ORM\Id
     */
    public $poll;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public $user;

    /**
     * @var int
     * @ORM\Column(name="vote", type="bigint", nullable=false)
     */
    public $vote;
}
