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
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     */
    public ?\DateTime $timestamp = null;

    /**
     * @ORM\Column(name="uniek", type="integer", nullable=false, options={"default"=0, "unsigned"=true})
     */
    public int $visitorsUnique = 0;

    /**
     * @ORM\Column(name="pageviews", type="integer", nullable=false, options={"default"=0, "unsigned"=true})
     */
    public int $visitorsTotal = 0;

    /**
     * @ORM\Column(name="pageviews_home", type="integer", nullable=false, options={"default"=0, "unsigned"=true})
     */
    public int $visitorsHome = 0;

    /**
     * @ORM\Column(name="pageviews_func", type="integer", nullable=false, options={"default"=0, "unsigned"=true})
     */
    public int $visitorsFunctions = 0;

    /**
     * @ORM\Column(name="spots", type="integer", nullable=false, options={"default"=0, "unsigned"=true})
     */
    public int $numberOfSpots = 0;

    /**
     * @ORM\Column(name="posts", type="integer", nullable=false, options={"default"=0, "unsigned"=true})
     */
    public int $numberOfPosts = 0;
}
