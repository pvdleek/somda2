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
     * @var int|null
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="uniek", type="bigint", nullable=false)
     */
    public string $visitorsUnique = '0';

    /**
     * @var string
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    public string $visitorsTotal = '0';

    /**
     * @var string
     * @ORM\Column(name="pageviews_home", type="bigint", nullable=false)
     */
    public string $visitorsHome = '0';

    /**
     * @var string
     * @ORM\Column(name="pageviews_func", type="bigint", nullable=false)
     */
    public string $visitorsFunctions = '0';

    /**
     * @var string
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    public string $numberOfSpots = '0';

    /**
     * @var string
     * @ORM\Column(name="posts", type="bigint", nullable=false)
     */
    public string $numberOfPosts = '0';
}
