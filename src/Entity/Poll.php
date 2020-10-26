<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pol_poll")
 * @ORM\Entity
 */
class Poll
{
    /**
     * @var int|null
     * @ORM\Column(name="pol_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="pol_question", type="string", length=200, nullable=false)
     */
    public string $question = '';

    /**
     * @var string
     * @ORM\Column(name="pol_option_a", type="string", length=150, nullable=false)
     */
    public string $optionA = '';

    /**
     * @var string
     * @ORM\Column(name="pol_option_b", type="string", length=150, nullable=false)
     */
    public string $optionB = '';

    /**
     * @var string
     * @ORM\Column(name="pol_option_c", type="string", length=150, nullable=false)
     */
    public string $optionC = '';

    /**
     * @var string
     * @ORM\Column(name="pol_option_d", type="string", length=150, nullable=false)
     */
    public string $optionD = '';

    /**
     * @var DateTime
     * @ORM\Column(name="pol_timestamp", type="date", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var PollVote[]
     * @ORM\OneToMany(targetEntity="App\Entity\PollVote", mappedBy="poll")
     */
    private $votes;

    /**
     *
     */
    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    /**
     * @param PollVote $pollVote
     * @return Poll
     */
    public function addVote(PollVote $pollVote): Poll
    {
        $this->votes[] = $pollVote;
        return $this;
    }

    /**
     * @return PollVote[]
     */
    public function getVotes(): array
    {
        return $this->votes->toArray();
    }
}
