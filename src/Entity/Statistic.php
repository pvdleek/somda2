<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="sta_statistic",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_sta_timestamp", columns={"sta_timestamp"})}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\Statistic")
 */
class Statistic
{
    /**
     * @var int|null
     * @ORM\Column(name="sta_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="sta_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string
     * @ORM\Column(name="sta_visitors_unique", type="bigint", nullable=false)
     */
    public string $visitorsUnique = '0';

    /**
     * @var string
     * @ORM\Column(name="sta_visitors_total", type="bigint", nullable=false)
     */
    public string $visitorsTotal = '0';

    /**
     * @var string
     * @ORM\Column(name="sta_visitors_home", type="bigint", nullable=false)
     */
    public string $visitorsHome = '0';

    /**
     * @var string
     * @ORM\Column(name="sta_visitor_functions", type="bigint", nullable=false)
     */
    public string $visitorsFunctions = '0';

    /**
     * @var string
     * @ORM\Column(name="sta_number_of_spots", type="bigint", nullable=false)
     */
    public string $numberOfSpots = '0';

    /**
     * @var string
     * @ORM\Column(name="sta_number_of_posts", type="bigint", nullable=false)
     */
    public string $numberOfPosts = '0';
}
