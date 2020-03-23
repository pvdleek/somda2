<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_changes")
 * @ORM\Entity
 */
class TrainCompositionProposition
{
    /**
     * @var TrainComposition
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainComposition")
     * @ORM\JoinColumn(name="matsmsid", referencedColumnName="matsmsid")
     * @ORM\Id
     */
    private $composition;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $date;

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
     * @var string|null
     * @ORM\Column(name="opmerkingen", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @return TrainComposition
     */
    public function getComposition(): TrainComposition
    {
        return $this->composition;
    }

    /**
     * @param TrainComposition $composition
     * @return TrainCompositionProposition
     */
    public function setComposition(TrainComposition $composition): TrainCompositionProposition
    {
        $this->composition = $composition;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return TrainCompositionProposition
     */
    public function setUser(User $user): TrainCompositionProposition
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return TrainCompositionProposition
     */
    public function setDate(DateTime $date): TrainCompositionProposition
    {
        $this->date = $date;
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
     * @return TrainCompositionProposition
     */
    public function setCar1(?string $car1): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar2(?string $car2): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar3(?string $car3): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar4(?string $car4): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar5(?string $car5): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar6(?string $car6): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar7(?string $car7): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar8(?string $car8): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar9(?string $car9): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar10(?string $car10): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar11(?string $car11): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar12(?string $car12): TrainCompositionProposition
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
     * @return TrainCompositionProposition
     */
    public function setCar13(?string $car13): TrainCompositionProposition
    {
        $this->car13 = $car13;
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
     * @return TrainCompositionProposition
     */
    public function setNote(?string $note): TrainCompositionProposition
    {
        $this->note = $note;
        return $this;
    }
}
