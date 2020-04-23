<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_tdr_trein_mat")
 * @ORM\Entity
 */
class RouteTrain
{
    /**
     * @var TrainTable
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @ORM\Id
     */
    public TrainTable $trainTableYear;

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
     * @ORM\Column(name="dag", type="bigint", nullable=false)
     * @ORM\Id
     */
    public int $dayNumber;

    /**
     * @var int
     * @ORM\Column(name="spots", type="bigint", nullable=false)
     */
    public int $numberOfSpots = 0;

    /**
     * @var TrainNameRange
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNameRange")
     * @ORM\JoinColumn(name="mat_naam_id", referencedColumnName="id")
     */
    public TrainNameRange $trainNameRange;

    /**
     * @var TrainTypeNamePattern
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTypeNamePattern")
     * @ORM\JoinColumn(name="mat_type_id", referencedColumnName="id")
     */
    public TrainTypeNamePattern $trainTypeNamePattern;
}
