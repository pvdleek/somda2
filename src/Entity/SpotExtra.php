<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_spots_extra")
 * @ORM\Entity
 */
class SpotExtra
{
    /**
     * @var Spot
     * @ORM\OneToOne(targetEntity="App\Entity\Spot", inversedBy="extra")
     * @ORM\JoinColumn(name="spotid", referencedColumnName="spotid")
     * @ORM\Id
     */
    private $spot;

    /**
     * @var string
     * @ORM\Column(name="extra", type="string", length=255, nullable=false)
     */
    private $extra = '';

    /**
     * @var string
     * @ORM\Column(name="user_extra", type="string", length=255, nullable=false)
     */
    private $userExtra = '';

    /**
     * @return Spot
     */
    public function getSpot(): Spot
    {
        return $this->spot;
    }

    /**
     * @param Spot $spot
     * @return SpotExtra
     */
    public function setSpot(Spot $spot): SpotExtra
    {
        $this->spot = $spot;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtra(): string
    {
        return $this->extra;
    }

    /**
     * @param string $extra
     * @return SpotExtra
     */
    public function setExtra(string $extra): SpotExtra
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserExtra(): string
    {
        return $this->userExtra;
    }

    /**
     * @param string $userExtra
     * @return SpotExtra
     */
    public function setUserExtra(string $userExtra): SpotExtra
    {
        $this->userExtra = $userExtra;
        return $this;
    }
}
