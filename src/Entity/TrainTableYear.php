<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrainTableYearRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrainTableYearRepository::class)]
#[ORM\Table(name: 'somda_tdr_drgl')]
class TrainTableYear
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tdr_nr', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'naam', length: 10, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(name: 'start_datum', type: 'date', nullable: true)]
    public ?\DateTime $start_date = null;

    #[ORM\Column(name: 'eind_datum', type: 'date', nullable: true)]
    public ?\DateTime $end_date = null;

    /**
     * @throws \Exception
     */
    public function isActive(): bool
    {
        return $this->start_date <= new \DateTime() && $this->end_date >= new \DateTime();
    }
}
