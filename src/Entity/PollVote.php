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
     * @ORM\ManyToOne(targetEntity="App\Entity\Poll", inversedBy="votes")
     * @ORM\JoinColumn(name="pollid", referencedColumnName="pollid")
     * @ORM\Id
     */
    public ?Poll $poll = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public ?User $user = null;

    /**
     * @ORM\Column(name="vote", type="smallint", nullable=false, options={"default"="0", "unsigned"=true})
     */
    public int $vote = 0;
}
