<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_rijdagen")
 * @ORM\Entity(repositoryClass="App\Repository\RouteOperationDays")
 */
class RouteOperationDays extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="rijdagenid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var bool
     * @ORM\Column(name="ma", type="boolean", nullable=false)
     */
    public bool $monday = false;

    /**
     * @var bool
     * @ORM\Column(name="di", type="boolean", nullable=false)
     */
    public bool $tuesday = false;

    /**
     * @var bool
     * @ORM\Column(name="wo", type="boolean", nullable=false)
     */
    public bool $wednesday = false;

    /**
     * @var bool
     * @ORM\Column(name="do", type="boolean", nullable=false)
     */
    public bool $thursday = false;

    /**
     * @var bool
     * @ORM\Column(name="vr", type="boolean", nullable=false)
     */
    public bool $friday = false;

    /**
     * @var bool
     * @ORM\Column(name="za", type="boolean", nullable=false)
     */
    public bool $saturday = false;

    /**
     * @var bool
     * @ORM\Column(name="zf", type="boolean", nullable=false)
     */
    public bool $sunday = false;

    /**
     * @param int $dayNumber
     * @return bool
     */
    public function isRunningOnDay(int $dayNumber): bool
    {
        switch ($dayNumber) {
            case 0:
                $day = $this->monday;
                break;
            case 1:
                $day = $this->tuesday;
                break;
            case 2:
                $day = $this->wednesday;
                break;
            case 3:
                $day = $this->thursday;
                break;
            case 4:
                $day = $this->friday;
                break;
            case 5:
                $day = $this->saturday;
                break;
            default:
                $day = $this->sunday;
                break;
        }
        return $day;
    }
}
