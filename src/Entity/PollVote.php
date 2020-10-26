<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pov_poll_vote", indexes={
 *     @ORM\Index(name="IDX_pov_pol_id", columns={"pov_pol_id"}),
 *     @ORM\Index(name="IDX_pov_use_id", columns={"pov_use_id"}),
 * })
 * @ORM\Entity
 */
class PollVote
{
    /**
     * @var Poll
     * @ORM\ManyToOne(targetEntity="App\Entity\Poll", inversedBy="votes")
     * @ORM\JoinColumn(name="pov_pol_id", referencedColumnName="pol_id")
     * @ORM\Id
     */
    public Poll $poll;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="pov_use_id", referencedColumnName="use_id")
     * @ORM\Id
     */
    public User $user;

    /**
     * @var int
     * @ORM\Column(name="pov_vote", type="integer", nullable=false, options={"default"="0"})
     */
    public int $vote = 0;
}
