<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_rijdagen")
 * @ORM\Entity
 */
class RouteOperationDays
{
    /**
     * @var int
     * @ORM\Column(name="rijdagenid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     * @ORM\Column(name="ma", type="bigint", nullable=true)
     */
    private $monday;

    /**
     * @var int|null
     * @ORM\Column(name="di", type="bigint", nullable=true)
     */
    private $tuesday;

    /**
     * @var int|null
     * @ORM\Column(name="wo", type="bigint", nullable=true)
     */
    private $wednesday;

    /**
     * @var int|null
     * @ORM\Column(name="do", type="bigint", nullable=true)
     */
    private $thursday;

    /**
     * @var int|null
     * @ORM\Column(name="vr", type="bigint", nullable=true)
     */
    private $friday;

    /**
     * @var int|null
     * @ORM\Column(name="za", type="bigint", nullable=true)
     */
    private $saturday;

    /**
     * @var int|null
     * @ORM\Column(name="zf", type="bigint", nullable=true)
     */
    private $sunday;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return RouteOperationDays
     */
    public function setId(int $id): RouteOperationDays
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMonday(): ?int
    {
        return $this->monday;
    }

    /**
     * @param int|null $monday
     * @return RouteOperationDays
     */
    public function setMonday(?int $monday): RouteOperationDays
    {
        $this->monday = $monday;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTuesday(): ?int
    {
        return $this->tuesday;
    }

    /**
     * @param int|null $tuesday
     * @return RouteOperationDays
     */
    public function setTuesday(?int $tuesday): RouteOperationDays
    {
        $this->tuesday = $tuesday;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWednesday(): ?int
    {
        return $this->wednesday;
    }

    /**
     * @param int|null $wednesday
     * @return RouteOperationDays
     */
    public function setWednesday(?int $wednesday): RouteOperationDays
    {
        $this->wednesday = $wednesday;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getThursday(): ?int
    {
        return $this->thursday;
    }

    /**
     * @param int|null $thursday
     * @return RouteOperationDays
     */
    public function setThursday(?int $thursday): RouteOperationDays
    {
        $this->thursday = $thursday;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFriday(): ?int
    {
        return $this->friday;
    }

    /**
     * @param int|null $friday
     * @return RouteOperationDays
     */
    public function setFriday(?int $friday): RouteOperationDays
    {
        $this->friday = $friday;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSaturday(): ?int
    {
        return $this->saturday;
    }

    /**
     * @param int|null $saturday
     * @return RouteOperationDays
     */
    public function setSaturday(?int $saturday): RouteOperationDays
    {
        $this->saturday = $saturday;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSunday(): ?int
    {
        return $this->sunday;
    }

    /**
     * @param int|null $sunday
     * @return RouteOperationDays
     */
    public function setSunday(?int $sunday): RouteOperationDays
    {
        $this->sunday = $sunday;
        return $this;
    }

    /**
     * @param int $dayNumber
     * @return bool
     */
    public function getDay(int $dayNumber) : bool
    {
        switch ($dayNumber) {
            case 0:
                return $this->getMonday();
            case 1:
                return $this->getTuesday();
            case 2:
                return $this->getWednesday();
            case 3:
                return $this->getThursday();
            case 4:
                return $this->getFriday();
            case 5:
                return $this->getSaturday();
            default:
                return $this->getSunday();
        }
    }
}
