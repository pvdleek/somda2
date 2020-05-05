<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_stats", uniqueConstraints={@ORM\UniqueConstraint(name="idx_date", columns={"datum"})})
 * @ORM\Entity(repositoryClass="App\Repository\Statistic")
 */
class Statistic extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var int
     * @ORM\Column(name="uniek", type="bigint", nullable=false)
     */
    public int $visitorsUnique = 0;

    /**
     * @var int
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    public int $visitorsTotal = 0;

    /**
     * @var int
     * @ORM\Column(name="pageviews_home", type="bigint", nullable=false)
     */
    public int $visitorsHome = 0;

    /**
     * @var int
     * @ORM\Column(name="pageviews_func", type="bigint", nullable=false)
     */
    public int $visitorsFunctions = 0;

    /**
     * @var int
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    public int $numberOfSpots = 0;

    /**
     * @var int
     * @ORM\Column(name="posts", type="bigint", nullable=false)
     */
    public int $numberOfPosts = 0;
}
