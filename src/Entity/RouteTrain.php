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
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="tdr_nr", referencedColumnName="tdr_nr")
     * @ORM\Id
     */
    public ?TrainTableYear $trainTableYear = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Route")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @ORM\Id
     */
    public ?Route $route = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="posid", referencedColumnName="posid")
     * @ORM\Id
     */
    public ?Position $position = null;

    /**
     * @ORM\Column(name="dag", type="smallint", nullable=false, options={"default"="1", "unsigned"=true})
     * @ORM\Id
     */
    public int $dayNumber = 1;

    /**
     * @ORM\Column(name="spots", type="integer", nullable=false, options={"unsigned"=true})
     */
    public int $numberOfSpots = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNamePattern")
     * @ORM\JoinColumn(name="mat_pattern_id", referencedColumnName="id")
     */
    public ?TrainNamePattern $trainNamePattern = null;
}
