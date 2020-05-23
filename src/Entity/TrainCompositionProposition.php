<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_changes")
 * @ORM\Entity
 */
class TrainCompositionProposition extends TrainCompositionBase
{
    /**
     * @var TrainComposition
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainComposition", inversedBy="propositions")
     * @ORM\JoinColumn(name="matsmsid", referencedColumnName="matsmsid")
     * @ORM\Id
     */
    public TrainComposition $composition;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
     * @ORM\Id
     */
    public User $user;

    /**
     * @var DateTime
     * @ORM\Column(name="datum", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string|null
     * @ORM\Column(name="bak1", type="string", length=15, nullable=true)
     */
    public ?string $car1;

    /**
     * @var string|null
     * @ORM\Column(name="bak2", type="string", length=15, nullable=true)
     */
    public ?string $car2;

    /**
     * @var string|null
     * @ORM\Column(name="bak3", type="string", length=15, nullable=true)
     */
    public ?string $car3;

    /**
     * @var string|null
     * @ORM\Column(name="bak4", type="string", length=15, nullable=true)
     */
    public ?string $car4;

    /**
     * @var string|null
     * @ORM\Column(name="bak5", type="string", length=15, nullable=true)
     */
    public ?string $car5;

    /**
     * @var string|null
     * @ORM\Column(name="bak6", type="string", length=15, nullable=true)
     */
    public ?string $car6;

    /**
     * @var string|null
     * @ORM\Column(name="bak7", type="string", length=15, nullable=true)
     */
    public ?string $car7;

    /**
     * @var string|null
     * @ORM\Column(name="bak8", type="string", length=15, nullable=true)
     */
    public ?string $car8;

    /**
     * @var string|null
     * @ORM\Column(name="bak9", type="string", length=15, nullable=true)
     */
    public ?string $car9;

    /**
     * @var string|null
     * @ORM\Column(name="bak10", type="string", length=15, nullable=true)
     */
    public ?string $car10;

    /**
     * @var string|null
     * @ORM\Column(name="bak11", type="string", length=15, nullable=true)
     */
    public ?string $car11;

    /**
     * @var string|null
     * @ORM\Column(name="bak12", type="string", length=15, nullable=true)
     */
    public ?string $car12;

    /**
     * @var string|null
     * @ORM\Column(name="bak13", type="string", length=15, nullable=true)
     */
    public ?string $car13;

    /**
     * @var string|null
     * @ORM\Column(name="opmerkingen", type="string", length=255, nullable=true)
     */
    public ?string $note;

    /**
     * @param TrainComposition $trainComposition
     */
    public function setFromTrainComposition(TrainComposition $trainComposition): void
    {
        $this->composition = $trainComposition;
        $this->car1 = $trainComposition->car1;
        $this->car2 = $trainComposition->car2;
        $this->car3 = $trainComposition->car3;
        $this->car4 = $trainComposition->car4;
        $this->car5 = $trainComposition->car5;
        $this->car6 = $trainComposition->car6;
        $this->car7 = $trainComposition->car7;
        $this->car8 = $trainComposition->car8;
        $this->car9 = $trainComposition->car9;
        $this->car10 = $trainComposition->car10;
        $this->car11 = $trainComposition->car11;
        $this->car12 = $trainComposition->car11;
        $this->car13 = $trainComposition->car11;
        $this->note = $trainComposition->note;
    }

    /**
     * @return TrainCompositionType
     */
    public function getType(): TrainCompositionType
    {
        return $this->composition->getType();
    }
}
