<?php
declare(strict_types=1);

namespace App\Entity;

abstract class TrainCompositionBase
{
    /**
     * @var string|null
     */
    public ?string $car1;

    /**
     * @var string|null
     */
    public ?string $car2;

    /**
     * @var string|null
     */
    public ?string $car3;

    /**
     * @var string|null
     */
    public ?string $car4;

    /**
     * @var string|null
     */
    public ?string $car5;

    /**
     * @var string|null
     */
    public ?string $car6;

    /**
     * @var string|null
     */
    public ?string $car7;

    /**
     * @var string|null
     */
    public ?string $car8;

    /**
     * @var string|null
     */
    public ?string $car9;

    /**
     * @var string|null
     */
    public ?string $car10;

    /**
     * @var string|null
     */
    public ?string $car11;

    /**
     * @var string|null
     */
    public ?string $car12;

    /**
     * @var string|null
     */
    public ?string $car13;

    /**
     * @var string|null
     */
    public ?string $note;

    /**
     * @param int $carNumber
     * @return string|null
     */
    public function getCar(int $carNumber): ?string
    {
        return $this->{'car' . $carNumber};
    }
}
