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
    private $poll;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(name="vote", type="bigint", nullable=false)
     */
    private $vote;

    /**
     * @return Poll
     */
    public function getPoll(): Poll
    {
        return $this->poll;
    }

    /**
     * @param Poll $poll
     * @return PollVote
     */
    public function setPoll(Poll $poll): PollVote
    {
        $this->poll = $poll;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return PollVote
     */
    public function setUser(User $user): PollVote
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getVote(): int
    {
        return $this->vote;
    }

    /**
     * @param int $vote
     * @return PollVote
     */
    public function setVote(int $vote): PollVote
    {
        $this->vote = $vote;
        return $this;
    }
}
