<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_poll", indexes={@ORM\Index(name="idx_48191_date", columns={"date"})})
 * @ORM\Entity
 */
class Poll
{
    /**
     * @var int
     * @ORM\Column(name="pollid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="question", type="string", length=200, nullable=false)
     */
    private $question = '';

    /**
     * @var string
     * @ORM\Column(name="opt_a", type="string", length=150, nullable=false)
     */
    private $optionA = '';

    /**
     * @var string
     * @ORM\Column(name="opt_b", type="string", length=150, nullable=false)
     */
    private $optionB = '';

    /**
     * @var string
     * @ORM\Column(name="opt_c", type="string", length=150, nullable=false)
     */
    private $optionC = '';

    /**
     * @var string
     * @ORM\Column(name="opt_d", type="string", length=150, nullable=false)
     */
    private $optionD = '';

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Poll
     */
    public function setId(int $id): Poll
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return Poll
     */
    public function setQuestion(string $question): Poll
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @return string
     */
    public function getOptionA(): string
    {
        return $this->optionA;
    }

    /**
     * @param string $optionA
     * @return Poll
     */
    public function setOptionA(string $optionA): Poll
    {
        $this->optionA = $optionA;
        return $this;
    }

    /**
     * @return string
     */
    public function getOptionB(): string
    {
        return $this->optionB;
    }

    /**
     * @param string $optionB
     * @return Poll
     */
    public function setOptionB(string $optionB): Poll
    {
        $this->optionB = $optionB;
        return $this;
    }

    /**
     * @return string
     */
    public function getOptionC(): string
    {
        return $this->optionC;
    }

    /**
     * @param string $optionC
     * @return Poll
     */
    public function setOptionC(string $optionC): Poll
    {
        $this->optionC = $optionC;
        return $this;
    }

    /**
     * @return string
     */
    public function getOptionD(): string
    {
        return $this->optionD;
    }

    /**
     * @param string $optionD
     * @return Poll
     */
    public function setOptionD(string $optionD): Poll
    {
        $this->optionD = $optionD;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Poll
     */
    public function setDate(DateTime $date): Poll
    {
        $this->date = $date;
        return $this;
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
