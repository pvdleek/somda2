<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="somda_tdr_s_e")
 * @ORM\Entity
 */
class TrainTableFirstLast
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="trainTableFirstLasts")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     * @ORM\Id
     */
    public Route $route;

    /**
     * @var int
     * @ORM\Column(name="dag", type="integer", nullable=false, options={"default"="1"})
     * @ORM\Id
     */
    public int $dayNumber = 1;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="v_locatieid", referencedColumnName="afkid")
     */
    public Location $firstLocation;

    /**
     * @var string
     * @ORM\Column(name="v_actie", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     */
    public string $firstAction = '-';

    /**
     * @var int
     * @ORM\Column(name="v_tijd", type="integer", nullable=false, options={"default"="0"})
     */
    public int $firstTime = 0;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(name="a_locatieid", referencedColumnName="afkid")
     */
    public Location $lastLocation;

    /**
     * @var string
     * @ORM\Column(name="a_actie", type="string", length=1, nullable=false, options={"default"="-"})
     * @Assert\Choice(choices=TrainTable::ACTION_VALUES)
     */
    public string $lastAction = '-';

    /**
     * @var int
     * @ORM\Column(name="a_tijd", type="integer", nullable=false, options={"default"="0"})
     */
    public int $lastTime = 0;
}
