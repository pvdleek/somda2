<?php
declare(strict_types=1);

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
    public Poll $poll;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public User $user;

    /**
     * @var int
     * @ORM\Column(name="vote", type="integer", nullable=false, options={"default"="0"})
     */
    public int $vote = 0;
}
