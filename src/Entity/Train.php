<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrainRepository::class)]
#[ORM\Table(
    name: 'somda_mat',
    uniqueConstraints: [new ORM\UniqueConstraint(name: 'unq_somda_mat__nummer', columns: ['nummer'])],
    indexes: [new ORM\Index(name: 'idx_somda_mat__vervoerder_id', columns: ['vervoerder_id'])]
)]
class Train
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'matid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'nummer', length: 20, nullable: false, options: ['default' => ''])]
    public string $number = '';

    #[ORM\Column(name: 'naam', length: 50, nullable: true)]
    public ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Transporter::class, inversedBy: 'trains')]
    #[ORM\JoinColumn(name: 'vervoerder_id', referencedColumnName: 'vervoerder_id')]
    public ?Transporter $transporter = null;

    #[ORM\ManyToOne(targetEntity: TrainNamePattern::class)]
    #[ORM\JoinColumn(name: 'pattern_id', referencedColumnName: 'id')]
    public ?TrainNamePattern $name_pattern = null;

    #[ORM\OneToMany(targetEntity: Spot::class, mappedBy: 'train')]
    private Collection $spots;

    public function __construct()
    {
        $this->spots = new ArrayCollection();
    }

    public function addSpot(Spot $spot): Train
    {
        $this->spots[] = $spot;
        return $this;
    }

    /**
     * @return Spot[]
     */
    public function getSpots(): array
    {
        return $this->spots->toArray();
    }
}
