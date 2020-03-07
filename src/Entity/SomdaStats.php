<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SomdaStats
 *
 * @ORM\Table(name="somda_stats")
 * @ORM\Entity
 */
class SomdaStats
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datum", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $datum;

    /**
     * @var int
     *
     * @ORM\Column(name="uniek", type="bigint", nullable=false)
     */
    private $uniek = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="pageviews", type="bigint", nullable=false)
     */
    private $pageviews = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="pageviews_home", type="bigint", nullable=false)
     */
    private $pageviewsHome = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="pageviews_func", type="bigint", nullable=false)
     */
    private $pageviewsFunc = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    private $spots = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="posts", type="bigint", nullable=false)
     */
    private $posts = '0';


}
