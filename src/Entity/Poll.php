<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_poll", indexes={@ORM\Index(name="idx_48191_date", columns={"date"})})
 * @ORM\Entity
 */
class Poll
{
    /**
     * @ORM\Column(name="pollid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="question", type="string", length=200, nullable=false)
     */
    public string $question = '';

    /**
     * @ORM\Column(name="opt_a", type="string", length=150, nullable=false)
     */
    public string $optionA = '';

    /**
     * @ORM\Column(name="opt_b", type="string", length=150, nullable=false)
     */
    public string $optionB = '';

    /**
     * @ORM\Column(name="opt_c", type="string", length=150, nullable=false)
     */
    public string $optionC = '';

    /**
     * @ORM\Column(name="opt_d", type="string", length=150, nullable=false)
     */
    public string $optionD = '';

    /**
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PollVote", mappedBy="poll")
     */
    private $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

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
