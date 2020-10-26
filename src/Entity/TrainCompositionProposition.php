<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tcp_train_composition_proposition", indexes={
 *     @ORM\Index(name="IDX_tcp_trc_id", columns={"tcp_trc_id"}),
 *     @ORM\Index(name="IDX_tcp_use_id", columns={"tcp_use_id"}),
 * })
 * @ORM\Entity
 */
class TrainCompositionProposition extends TrainCompositionBase
{
    /**
     * @var TrainComposition
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainComposition", inversedBy="propositions")
     * @ORM\JoinColumn(name="tcp_trc_id", referencedColumnName="trc_id")
     * @ORM\Id
     */
    public TrainComposition $composition;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="tcp_use_id", referencedColumnName="use_id")
     * @ORM\Id
     */
    public User $user;

    /**
     * @var DateTime
     * @ORM\Column(name="tcp_timestamp", type="datetime", nullable=false)
     */
    public DateTime $timestamp;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_1", type="string", length=15, nullable=true)
     */
    public ?string $car1;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_2", type="string", length=15, nullable=true)
     */
    public ?string $car2;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_3", type="string", length=15, nullable=true)
     */
    public ?string $car3;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_4", type="string", length=15, nullable=true)
     */
    public ?string $car4;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_5", type="string", length=15, nullable=true)
     */
    public ?string $car5;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_6", type="string", length=15, nullable=true)
     */
    public ?string $car6;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_7", type="string", length=15, nullable=true)
     */
    public ?string $car7;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_8", type="string", length=15, nullable=true)
     */
    public ?string $car8;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_9", type="string", length=15, nullable=true)
     */
    public ?string $car9;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_10", type="string", length=15, nullable=true)
     */
    public ?string $car10;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_11", type="string", length=15, nullable=true)
     */
    public ?string $car11;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_12", type="string", length=15, nullable=true)
     */
    public ?string $car12;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_car_13", type="string", length=15, nullable=true)
     */
    public ?string $car13;

    /**
     * @var string|null
     * @ORM\Column(name="tcp_note", type="string", length=255, nullable=true)
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
