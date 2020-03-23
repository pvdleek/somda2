<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_types")
 * @ORM\Entity
 */
class TrainCompositionType
{
    /**
     * @var int
     * @ORM\Column(name="typeid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     */
    private $description = '';

    /**
     * @var string|null
     * @ORM\Column(name="bak1", type="string", length=25, nullable=true)
     */
    private $car1;

    /**
     * @var string|null
     * @ORM\Column(name="bak2", type="string", length=25, nullable=true)
     */
    private $car2;

    /**
     * @var string|null
     * @ORM\Column(name="bak3", type="string", length=25, nullable=true)
     */
    private $car3;

    /**
     * @var string|null
     * @ORM\Column(name="bak4", type="string", length=25, nullable=true)
     */
    private $car4;

    /**
     * @var string|null
     * @ORM\Column(name="bak5", type="string", length=25, nullable=true)
     */
    private $car5;

    /**
     * @var string|null
     * @ORM\Column(name="bak6", type="string", length=25, nullable=true)
     */
    private $car6;

    /**
     * @var string|null
     * @ORM\Column(name="bak7", type="string", length=25, nullable=true)
     */
    private $car7;

    /**
     * @var string|null
     * @ORM\Column(name="bak8", type="string", length=25, nullable=true)
     */
    private $car8;

    /**
     * @var string|null
     * @ORM\Column(name="bak9", type="string", length=25, nullable=true)
     */
    private $car9;

    /**
     * @var string|null
     * @ORM\Column(name="bak10", type="string", length=25, nullable=true)
     */
    private $car10;

    /**
     * @var string|null
     * @ORM\Column(name="bak11", type="string", length=25, nullable=true)
     */
    private $car11;

    /**
     * @var string|null
     * @ORM\Column(name="bak12", type="string", length=25, nullable=true)
     */
    private $car12;

    /**
     * @var string|null
     * @ORM\Column(name="bak13", type="string", length=25, nullable=true)
     */
    private $car13;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TrainCompositionType
     */
    public function setId(int $id): TrainCompositionType
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return TrainCompositionType
     */
    public function setDescription(string $description): TrainCompositionType
    {
        $this->description = $description;
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
     * @return TrainCompositionType
     */
    public function setCar1(?string $car1): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar2(?string $car2): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar3(?string $car3): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar4(?string $car4): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar5(?string $car5): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar6(?string $car6): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar7(?string $car7): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar8(?string $car8): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar9(?string $car9): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar10(?string $car10): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar11(?string $car11): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar12(?string $car12): TrainCompositionType
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
     * @return TrainCompositionType
     */
    public function setCar13(?string $car13): TrainCompositionType
    {
        $this->car13 = $car13;
        return $this;
    }
}
