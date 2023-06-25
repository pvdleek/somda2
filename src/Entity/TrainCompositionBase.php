<?php
declare(strict_types=1);

namespace App\Entity;

abstract class TrainCompositionBase
{
    public ?string $car1 = null;

    public ?string $car2 = null;

    public ?string $car3 = null;

    public ?string $car4 = null;

    public ?string $car5 = null;

    public ?string $car6 = null;

    public ?string $car7 = null;

    public ?string $car8 = null;

    public ?string $car9 = null;

    public ?string $car10 = null;

    public ?string $car11 = null;

    public ?string $car12 = null;

    public ?string $car13 = null;

    public ?string $note;

    public function getCar(int $carNumber): ?string
    {
        return $this->{'car' . $carNumber};
    }
}
