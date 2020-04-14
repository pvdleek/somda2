<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_stats", uniqueConstraints={@ORM\UniqueConstraint(name="idx_date", columns={"datum"})})
 * @ORM\Entity(repositoryClass="App\Repository\Statistic")
 */
class Statistic
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $date;

    /**
     * @var int
     * @ORM\Column(name="uniek", type="bigint", nullable=false)
     */
    private $visitorsUnique = 0;

    /**
     * @var int
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    private $visitorsTotal = 0;

    /**
     * @var int
     * @ORM\Column(name="pageviews_home", type="bigint", nullable=false)
     */
    private $visitorsHome = 0;

    /**
     * @var int
     * @ORM\Column(name="pageviews_func", type="bigint", nullable=false)
     */
    private $visitorsFunctions = 0;

    /**
     * @var int
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    private $numberOfSpots = 0;

    /**
     * @var int
     * @ORM\Column(name="posts", type="bigint", nullable=false)
     */
    private $numberOfPosts = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Statistic
     */
    public function setId(int $id): Statistic
    {
        $this->id = $id;
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
     * @return Statistic
     */
    public function setDate(DateTime $date): Statistic
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getVisitorsUnique(): int
    {
        return $this->visitorsUnique;
    }

    /**
     * @param int $visitorsUnique
     * @return Statistic
     */
    public function setVisitorsUnique(int $visitorsUnique): Statistic
    {
        $this->visitorsUnique = $visitorsUnique;
        return $this;
    }

    /**
     * @return int
     */
    public function getVisitorsTotal(): int
    {
        return $this->visitorsTotal;
    }

    /**
     * @param int $visitorsTotal
     * @return Statistic
     */
    public function setVisitorsTotal(int $visitorsTotal): Statistic
    {
        $this->visitorsTotal = $visitorsTotal;
        return $this;
    }

    /**
     * @return int
     */
    public function getVisitorsHome(): int
    {
        return $this->visitorsHome;
    }

    /**
     * @param int $visitorsHome
     * @return Statistic
     */
    public function setVisitorsHome(int $visitorsHome): Statistic
    {
        $this->visitorsHome = $visitorsHome;
        return $this;
    }

    /**
     * @return int
     */
    public function getVisitorsFunctions(): int
    {
        return $this->visitorsFunctions;
    }

    /**
     * @param int $visitorsFunctions
     * @return Statistic
     */
    public function setVisitorsFunctions(int $visitorsFunctions): Statistic
    {
        $this->visitorsFunctions = $visitorsFunctions;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfSpots(): int
    {
        return $this->numberOfSpots;
    }

    /**
     * @param int $numberOfSpots
     * @return Statistic
     */
    public function setNumberOfSpots(int $numberOfSpots): Statistic
    {
        $this->numberOfSpots = $numberOfSpots;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfPosts(): int
    {
        return $this->numberOfPosts;
    }

    /**
     * @param int $numberOfPosts
     * @return Statistic
     */
    public function setNumberOfPosts(int $numberOfPosts): Statistic
    {
        $this->numberOfPosts = $numberOfPosts;
        return $this;
    }
}
