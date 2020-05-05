<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spots", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48259_treinid", columns={"treinid", "posid", "locatieid", "matid", "uid", "datum"})}, indexes={@ORM\Index(name="idx_48259_matid", columns={"matid"}), @ORM\Index(name="idx_48259_datum", columns={"datum"}), @ORM\Index(name="idx_48259_uid", columns={"uid"})})
 * @ORM\Entity(repositoryClass="App\Repository\Spot")
 */
class Spot extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="spotid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var Train
     * @ORM\ManyToOne(targetEntity="App\Entity\Train", inversedBy="spots")
     * @ORM\JoinColumn(name="matid", referencedColumnName="matid")
     */
    public Train $train;

    /**
     * @var Route
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="spots")
     * @ORM\JoinColumn(name="treinid", referencedColumnName="treinid")
     */
    public Route $route;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="App\Entity\Position")
     * @ORM\JoinColumn(name="posid", referencedColumnName="posid")
     */
    public Position $position;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="spots")
     * @ORM\JoinColumn(name="locatieid", referencedColumnName="afkid")
     */
    public Location $location;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="spots")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     */
    public User $user;

    /**
     * @var SpotExtra|null
     * @ORM\OneToOne(targetEntity="App\Entity\SpotExtra", mappedBy="spot")
     */
    public ?SpotExtra $extra;
}
