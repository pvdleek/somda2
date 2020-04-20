<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_rijdagen")
 * @ORM\Entity
 */
class RouteOperationDays extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="rijdagenid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(name="ma", type="boolean", nullable=false)
     */
    public $monday = false;

    /**
     * @var bool
     * @ORM\Column(name="di", type="boolean", nullable=false)
     */
    public $tuesday = false;

    /**
     * @var bool
     * @ORM\Column(name="wo", type="boolean", nullable=false)
     */
    public $wednesday = false;

    /**
     * @var bool
     * @ORM\Column(name="do", type="boolean", nullable=false)
     */
    public $thursday = false;

    /**
     * @var bool
     * @ORM\Column(name="vr", type="boolean", nullable=false)
     */
    public $friday = false;

    /**
     * @var bool
     * @ORM\Column(name="za", type="boolean", nullable=false)
     */
    public $saturday = false;

    /**
     * @var bool
     * @ORM\Column(name="zf", type="boolean", nullable=false)
     */
    public $sunday = false;

    /**
     * @param int $dayNumber
     * @return bool
     */
    public function getDay(int $dayNumber): bool
    {
        switch ($dayNumber) {
            case 0:
                return $this->monday;
            case 1:
                return $this->tuesday;
            case 2:
                return $this->wednesday;
            case 3:
                return $this->thursday;
            case 4:
                return $this->friday;
            case 5:
                return $this->saturday;
        }
        return $this->sunday;
    }
}
