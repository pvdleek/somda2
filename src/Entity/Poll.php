<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_poll", indexes={@ORM\Index(name="idx_48191_date", columns={"date"})})
 * @ORM\Entity
 */
class Poll extends Entity
{
    /**
     * @var int|null
     * @ORM\Column(name="pollid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="question", type="string", length=200, nullable=false)
     */
    public string $question = '';

    /**
     * @var string
     * @ORM\Column(name="opt_a", type="string", length=150, nullable=false)
     */
    public string $optionA = '';

    /**
     * @var string
     * @ORM\Column(name="opt_b", type="string", length=150, nullable=false)
     */
    public string $optionB = '';

    /**
     * @var string
     * @ORM\Column(name="opt_c", type="string", length=150, nullable=false)
     */
    public string $optionC = '';

    /**
     * @var string
     * @ORM\Column(name="opt_d", type="string", length=150, nullable=false)
     */
    public string $optionD = '';

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
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
