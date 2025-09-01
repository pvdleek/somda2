<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_mat_changes')]
class TrainCompositionProposition extends TrainCompositionBase
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: TrainComposition::class, inversedBy: 'propositions')]
    #[ORM\JoinColumn(name: 'matsmsid', referencedColumnName: 'matsmsid')]
    public ?TrainComposition $composition = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    public ?User $user = null;

    #[ORM\Column(name: 'datum', nullable: true)]
    public ?\DateTime $timestamp = null;

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

    #[ORM\Column(name: 'opmerkingen', length: 255, nullable: true)]
    public ?string $note = null;

    public function setFromTrainComposition(TrainComposition $train_composition): void
    {
        $this->composition = $train_composition;
        $this->car1 = $train_composition->car1;
        $this->car2 = $train_composition->car2;
        $this->car3 = $train_composition->car3;
        $this->car4 = $train_composition->car4;
        $this->car5 = $train_composition->car5;
        $this->car6 = $train_composition->car6;
        $this->car7 = $train_composition->car7;
        $this->car8 = $train_composition->car8;
        $this->car9 = $train_composition->car9;
        $this->car10 = $train_composition->car10;
        $this->car11 = $train_composition->car11;
        $this->car12 = $train_composition->car12;
        $this->car13 = $train_composition->car13;
        $this->note = $train_composition->note;
    }

    public function getType(): TrainCompositionType
    {
        return $this->composition->getType();
    }
}
