<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_stats", uniqueConstraints={@ORM\UniqueConstraint(name="idx_date", columns={"datum"})})
 * @ORM\Entity(repositoryClass="App\Repository\Statistic")
 */
class Statistic
{
    /**
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\Column(name="uniek", type="bigint", nullable=false)
     */
    public int $visitorsUnique = 0;

    /**
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    public int $visitorsTotal = 0;

    /**
     * @ORM\Column(name="pageviews_home", type="bigint", nullable=false)
     */
    public int $visitorsHome = 0;

    /**
     * @ORM\Column(name="pageviews_func", type="bigint", nullable=false)
     */
    public int $visitorsFunctions = 0;

    /**
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    public int $numberOfSpots = 0;

    /**
     * @ORM\Column(name="posts", type="bigint", nullable=false)
     */
    public int $numberOfPosts = 0;
}
