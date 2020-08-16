<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_trein_mat")
 * @ORM\Entity
 */
class RouteTrain
{
    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @ORM\Id
     */
    public TrainTableYear $trainTableYear;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @ORM\Id
     */
    public Route $route;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="posid", referencedColumnName="posid")
     * @ORM\Id
     */
    public Position $position;

    /**
     * @var int
     * @ORM\Column(name="dag", type="integer", nullable=false, options={"default"="1"})
     * @ORM\Id
     */
    public int $dayNumber = 1;

    /**
     * @var int
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    public int $numberOfSpots = 0;

    /**
     * @var TrainNamePattern
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNamePattern")
     * @ORM\JoinColumn(name="mat_pattern_id", referencedColumnName="id")
     */
    public TrainNamePattern $trainNamePattern;
}
