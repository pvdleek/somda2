<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_sms", indexes={@ORM\Index(name="idx_48145_typeid", columns={"typeid"})})
 * @ORM\Entity
 */
class TrainComposition extends TrainCompositionBase
{
    public const NUMBER_OF_CARS = 13;

    /**
     * @var int|null
     * @ORM\Column(name="matsmsid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var TrainCompositionType
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainCompositionType")
     * @ORM\JoinColumn(name="typeid", referencedColumnName="typeid")
     */
    public TrainCompositionType $type;

    /**
     * @var string|null
     * @ORM\Column(name="bak1", type="string", length=15, nullable=true)
     */
    public ?string $car1 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak2", type="string", length=15, nullable=true)
     */
    public ?string $car2 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak3", type="string", length=15, nullable=true)
     */
    public ?string $car3 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak4", type="string", length=15, nullable=true)
     */
    public ?string $car4 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak5", type="string", length=15, nullable=true)
     */
    public ?string $car5 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak6", type="string", length=15, nullable=true)
     */
    public ?string $car6 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak7", type="string", length=15, nullable=true)
     */
    public ?string $car7 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak8", type="string", length=15, nullable=true)
     */
    public ?string $car8 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak9", type="string", length=15, nullable=true)
     */
    public ?string $car9 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak10", type="string", length=15, nullable=true)
     */
    public ?string $car10 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak11", type="string", length=15, nullable=true)
     */
    public ?string $car11 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak12", type="string", length=15, nullable=true)
     */
    public ?string $car12 = null;

    /**
     * @var string|null
     * @ORM\Column(name="bak13", type="string", length=15, nullable=true)
     */
    public ?string $car13 = null;

    /**
     * @var DateTime|null
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    public ?DateTime $lastUpdateTimestamp;

    /**
     * @var string|null
     * @ORM\Column(name="opmerkingen", type="string", length=255, nullable=true)
     */
    public ?string $note = null;

    /**
     * @var string|null
     * @ORM\Column(name="extra", type="string", length=255, nullable=true)
     */
    public ?string $extra = null;

    /**
     * @var bool
     * @ORM\Column(name="index_regel", type="boolean", nullable=false)
     */
    public bool $indexLine = false;

    /**
     * @var TrainCompositionProposition[]
     * @ORM\OneToMany(targetEntity="App\Entity\TrainCompositionProposition", mappedBy="composition")
     */
    private $propositions;

    /**
     *
     */
    public function __construct()
    {
        $this->propositions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return static
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return TrainCompositionType
     */
    public function getType(): TrainCompositionType
    {
        return $this->type;
    }

    /**
     * @param TrainCompositionProposition $proposition
     * @return TrainComposition
     */
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
