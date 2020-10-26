<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rot_route_train", indexes={
 *     @ORM\Index(name="IDX_rot_tty_id", columns={"rot_tty_id"}),
 *     @ORM\Index(name="IDX_rot_rou_id", columns={"rot_rou_id"}),
 *     @ORM\Index(name="IDX_rot_pos_id", columns={"rot_pos_id"}),
 *     @ORM\Index(name="IDX_rot_tnp_id", columns={"rot_tnp_id"}),
 * })
 * @ORM\Entity
 */
class RouteTrain
{
    /**
     * @var TrainTableYear
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainTableYear")
     * @ORM\JoinColumn(name="rot_tty_id", referencedColumnName="tty_id")
     * @ORM\Id
     */
    public TrainTableYear $trainTableYear;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route")
     * @ORM\JoinColumn(name="rot_rou_id", referencedColumnName="rou_id")
     * @ORM\Id
     */
    public Route $route;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="rot_pos_id", referencedColumnName="pos_id")
     * @ORM\Id
     */
    public Position $position;

    /**
     * @var int
     * @ORM\Column(name="rot_day_number", type="integer", nullable=false, options={"default"="1"})
     * @ORM\Id
     */
    public int $dayNumber = 1;

    /**
     * @var int
     * @ORM\Column(name="rot_number_of_spots", type="bigint", nullable=false)
     */
    public int $numberOfSpots = 0;

    /**
     * @var TrainNamePattern
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNamePattern")
     * @ORM\JoinColumn(name="rot_tnp_id", referencedColumnName="tnp_id")
     */
    public TrainNamePattern $trainNamePattern;
}
