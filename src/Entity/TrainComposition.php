<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_sms", indexes={@ORM\Index(name="idx_48145_typeid", columns={"typeid"})})
 * @ORM\Entity
 */
class TrainComposition
{
    /**
     * @var int
     * @ORM\Column(name="matsmsid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var TrainCompositionType
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainCompositionType")
     * @ORM\JoinColumn(name="typeid", referencedColumnName="typeid")
     */
    private $type;

    /**
     * @var string|null
     * @ORM\Column(name="bak1", type="string", length=15, nullable=true)
     */
    private $car1;

    /**
     * @var string|null
     * @ORM\Column(name="bak2", type="string", length=15, nullable=true)
     */
    private $car2;

    /**
     * @var string|null
     * @ORM\Column(name="bak3", type="string", length=15, nullable=true)
     */
    private $car3;

    /**
     * @var string|null
     * @ORM\Column(name="bak4", type="string", length=15, nullable=true)
     */
    private $car4;

    /**
     * @var string|null
     * @ORM\Column(name="bak5", type="string", length=15, nullable=true)
     */
    private $car5;

    /**
     * @var string|null
     * @ORM\Column(name="bak6", type="string", length=15, nullable=true)
     */
    private $car6;

    /**
     * @var string|null
     * @ORM\Column(name="bak7", type="string", length=15, nullable=true)
     */
    private $car7;

    /**
     * @var string|null
     * @ORM\Column(name="bak8", type="string", length=15, nullable=true)
     */
    private $car8;

    /**
     * @var string|null
     * @ORM\Column(name="bak9", type="string", length=15, nullable=true)
     */
    private $car9;

    /**
     * @var string|null
     * @ORM\Column(name="bak10", type="string", length=15, nullable=true)
     */
    private $car10;

    /**
     * @var string|null
     * @ORM\Column(name="bak11", type="string", length=15, nullable=true)
     */
    private $car11;

    /**
     * @var string|null
     * @ORM\Column(name="bak12", type="string", length=15, nullable=true)
     */
    private $car12;

    /**
     * @var string|null
     * @ORM\Column(name="bak13", type="string", length=15, nullable=true)
     */
    private $car13;

    /**
     * @var DateTime|null
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate;

    /**
     * @var string|null
     * @ORM\Column(name="opmerkingen", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @var string
     * @ORM\Column(name="extra", type="string", length=255, nullable=false)
     */
    private $extra = '';

    /**
     * @var boolean
     * @ORM\Column(name="index_regel", type="boolean", nullable=false)
     */
    private $indexLine = false;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TrainComposition
     */
    public function setId(int $id): TrainComposition
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
     * @param TrainCompositionType $type
     * @return TrainComposition
     */
    public function setType(TrainCompositionType $type): TrainComposition
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar1(): ?string
    {
        return $this->car1;
    }

    /**
     * @param string|null $car1
     * @return TrainComposition
     */
    public function setCar1(?string $car1): TrainComposition
    {
        $this->car1 = $car1;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar2(): ?string
    {
        return $this->car2;
    }

    /**
     * @param string|null $car2
     * @return TrainComposition
     */
    public function setCar2(?string $car2): TrainComposition
    {
        $this->car2 = $car2;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar3(): ?string
    {
        return $this->car3;
    }

    /**
     * @param string|null $car3
     * @return TrainComposition
     */
    public function setCar3(?string $car3): TrainComposition
    {
        $this->car3 = $car3;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar4(): ?string
    {
        return $this->car4;
    }

    /**
     * @param string|null $car4
     * @return TrainComposition
     */
    public function setCar4(?string $car4): TrainComposition
    {
        $this->car4 = $car4;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar5(): ?string
    {
        return $this->car5;
    }

    /**
     * @param string|null $car5
     * @return TrainComposition
     */
    public function setCar5(?string $car5): TrainComposition
    {
        $this->car5 = $car5;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar6(): ?string
    {
        return $this->car6;
    }

    /**
     * @param string|null $car6
     * @return TrainComposition
     */
    public function setCar6(?string $car6): TrainComposition
    {
        $this->car6 = $car6;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar7(): ?string
    {
        return $this->car7;
    }

    /**
     * @param string|null $car7
     * @return TrainComposition
     */
    public function setCar7(?string $car7): TrainComposition
    {
        $this->car7 = $car7;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar8(): ?string
    {
        return $this->car8;
    }

    /**
     * @param string|null $car8
     * @return TrainComposition
     */
    public function setCar8(?string $car8): TrainComposition
    {
        $this->car8 = $car8;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar9(): ?string
    {
        return $this->car9;
    }

    /**
     * @param string|null $car9
     * @return TrainComposition
     */
    public function setCar9(?string $car9): TrainComposition
    {
        $this->car9 = $car9;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar10(): ?string
    {
        return $this->car10;
    }

    /**
     * @param string|null $car10
     * @return TrainComposition
     */
    public function setCar10(?string $car10): TrainComposition
    {
        $this->car10 = $car10;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar11(): ?string
    {
        return $this->car11;
    }

    /**
     * @param string|null $car11
     * @return TrainComposition
     */
    public function setCar11(?string $car11): TrainComposition
    {
        $this->car11 = $car11;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar12(): ?string
    {
        return $this->car12;
    }

    /**
     * @param string|null $car12
     * @return TrainComposition
     */
    public function setCar12(?string $car12): TrainComposition
    {
        $this->car12 = $car12;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCar13(): ?string
    {
        return $this->car13;
    }

    /**
     * @param string|null $car13
     * @return TrainComposition
     */
    public function setCar13(?string $car13): TrainComposition
    {
        $this->car13 = $car13;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }

    /**
     * @param DateTime|null $lastUpdate
     * @return TrainComposition
     */
    public function setLastUpdate(?DateTime $lastUpdate): TrainComposition
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     * @return TrainComposition
     */
    public function setNote(?string $note): TrainComposition
    {
        $this->note = $note;
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
     * @return TrainComposition
     */
    public function setExtra(string $extra): TrainComposition
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIndexLine(): bool
    {
        return $this->indexLine;
    }

    /**
     * @param bool $indexLine
     * @return TrainComposition
     */
    public function setIndexLine(bool $indexLine): TrainComposition
    {
        $this->indexLine = $indexLine;
        return $this;
    }
}
