<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RouteOperationDaysRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteOperationDaysRepository::class)]
#[ORM\Table(name: 'somda_rijdagen')]
class RouteOperationDays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rijdagenid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'ma', nullable: false, options: ['default' => false])]
    public bool $monday = false;

    #[ORM\Column(name: 'di', nullable: false, options: ['default' => false])]
    public bool $tuesday = false;

    #[ORM\Column(name: 'wo', nullable: false, options: ['default' => false])]
    public bool $wednesday = false;

    #[ORM\Column(name: 'do', nullable: false, options: ['default' => false])]
    public bool $thursday = false;

    #[ORM\Column(name: 'vr', nullable: false, options: ['default' => false])]
    public bool $friday = false;

    #[ORM\Column(name: 'za', nullable: false, options: ['default' => false])]
    public bool $saturday = false;

    #[ORM\Column(name: 'zf', nullable: false, options: ['default' => false])]
    public bool $sunday = false;

    public function isRunningOnDay(int $day_number): bool
    {
        switch ($day_number) {
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
