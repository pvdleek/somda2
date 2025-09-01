<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_mat_sms', indexes: [new ORM\Index(name: 'unq_somda_mat_sms__typeid', columns: ['typeid'])])]
class TrainComposition extends TrainCompositionBase
{
    public const NUMBER_OF_CARS = 13;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'matsmsid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TrainCompositionType::class)]
    #[ORM\JoinColumn(name: 'typeid', referencedColumnName: 'typeid')]
    public ?TrainCompositionType $type = null;

    #[ORM\Column(name: 'bak1', length: 15, nullable: true)]
    public ?string $car1 = null;

    #[ORM\Column(name: 'bak2', length: 15, nullable: true)]
    public ?string $car2 = null;

    #[ORM\Column(name: 'bak3', length: 15, nullable: true)]
    public ?string $car3 = null;

    #[ORM\Column(name: 'bak4', length: 15, nullable: true)]
    public ?string $car4 = null;

    #[ORM\Column(name: 'bak5', length: 15, nullable: true)]
    public ?string $car5 = null;

    #[ORM\Column(name: 'bak6', length: 15, nullable: true)]
    public ?string $car6 = null;

    #[ORM\Column(name: 'bak7', length: 15, nullable: true)]
    public ?string $car7 = null;

    #[ORM\Column(name: 'bak8', length: 15, nullable: true)]
    public ?string $car8 = null;

    #[ORM\Column(name: 'bak9', length: 15, nullable: true)]
    public ?string $car9 = null;

    #[ORM\Column(name: 'bak10', length: 15, nullable: true)]
    public ?string $car10 = null;

    #[ORM\Column(name: 'bak11', length: 15, nullable: true)]
    public ?string $car11 = null;

    #[ORM\Column(name: 'bak12', length: 15, nullable: true)]
    public ?string $car12 = null;

    #[ORM\Column(name: 'bak13', length: 15, nullable: true)]
    public ?string $car13 = null;

    #[ORM\Column(name: 'last_update', nullable: true)]
    public ?\DateTime $last_update_timestamp = null;

    #[ORM\Column(name: 'opmerkingen', length: 255, nullable: true)]
    public ?string $note = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $extra = null;

    #[ORM\Column(name: 'index_regel', nullable: false, options: ['default' => false])]
    public bool $indexLine = false;

    #[ORM\OneToMany(targetEntity: TrainCompositionProposition::class, mappedBy: 'composition', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $propositions;

    public function __construct()
    {
        $this->propositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): TrainComposition
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): TrainCompositionType
    {
        return $this->type;
    }

    public function addProposition(TrainCompositionProposition $proposition): TrainComposition
    {
        $this->propositions[] = $proposition;
        $proposition->composition = $this;
        return $this;
    }

    /**
     * @return TrainCompositionProposition[]
     */
    public function getPropositions(): array
    {
        return $this->propositions->toArray();
    }
}
