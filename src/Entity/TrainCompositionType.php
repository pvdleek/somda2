<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tct_train_composition_type")
 * @ORM\Entity
 */
class TrainCompositionType
{
    /**
     * @var int|null
     * @ORM\Column(name="tct_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="tct_description", type="string", length=25, nullable=false)
     */
    public string $description = '';

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_1", type="string", length=25, nullable=true)
     */
    public ?string $car1;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_2", type="string", length=25, nullable=true)
     */
    public ?string $car2;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_3", type="string", length=25, nullable=true)
     */
    public ?string $car3;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_4", type="string", length=25, nullable=true)
     */
    public ?string $car4;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_5", type="string", length=25, nullable=true)
     */
    public ?string $car5;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_6", type="string", length=25, nullable=true)
     */
    public ?string $car6;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_7", type="string", length=25, nullable=true)
     */
    public ?string $car7;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_8", type="string", length=25, nullable=true)
     */
    public ?string $car8;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_9", type="string", length=25, nullable=true)
     */
    public ?string $car9;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_10", type="string", length=25, nullable=true)
     */
    public ?string $car10;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_11", type="string", length=25, nullable=true)
     */
    public ?string $car11;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_12", type="string", length=25, nullable=true)
     */
    public ?string $car12;

    /**
     * @var string|null
     * @ORM\Column(name="tct_car_13", type="string", length=25, nullable=true)
     */
    public ?string $car13;

    /**
     * @param int $carNumber
     * @return string|null
     */
    public function getCar(int $carNumber): ?string
    {
        return $this->{'car' . $carNumber};
    }
}
