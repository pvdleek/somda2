<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_types")
 * @ORM\Entity
 */
class TrainCompositionType extends Entity
{
    /**
     * @var int
     * @ORM\Column(name="typeid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     */
    public string $description = '';

    /**
     * @var string|null
     * @ORM\Column(name="bak1", type="string", length=25, nullable=true)
     */
    public ?string $car1;

    /**
     * @var string|null
     * @ORM\Column(name="bak2", type="string", length=25, nullable=true)
     */
    public ?string $car2;

    /**
     * @var string|null
     * @ORM\Column(name="bak3", type="string", length=25, nullable=true)
     */
    public ?string $car3;

    /**
     * @var string|null
     * @ORM\Column(name="bak4", type="string", length=25, nullable=true)
     */
    public ?string $car4;

    /**
     * @var string|null
     * @ORM\Column(name="bak5", type="string", length=25, nullable=true)
     */
    public ?string $car5;

    /**
     * @var string|null
     * @ORM\Column(name="bak6", type="string", length=25, nullable=true)
     */
    public ?string $car6;

    /**
     * @var string|null
     * @ORM\Column(name="bak7", type="string", length=25, nullable=true)
     */
    public ?string $car7;

    /**
     * @var string|null
     * @ORM\Column(name="bak8", type="string", length=25, nullable=true)
     */
    public ?string $car8;

    /**
     * @var string|null
     * @ORM\Column(name="bak9", type="string", length=25, nullable=true)
     */
    public ?string $car9;

    /**
     * @var string|null
     * @ORM\Column(name="bak10", type="string", length=25, nullable=true)
     */
    public ?string $car10;

    /**
     * @var string|null
     * @ORM\Column(name="bak11", type="string", length=25, nullable=true)
     */
    public ?string $car11;

    /**
     * @var string|null
     * @ORM\Column(name="bak12", type="string", length=25, nullable=true)
     */
    public ?string $car12;

    /**
     * @var string|null
     * @ORM\Column(name="bak13", type="string", length=25, nullable=true)
     */
    public ?string $car13;
}
