<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_types")
 * @ORM\Entity
 */
class TrainCompositionType
{
    /**
     * @ORM\Column(name="typeid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     */
    public string $description = '';

    /**
     * @ORM\Column(name="bak1", type="string", length=25, nullable=true)
     */
    public ?string $car1 = null;

    /**
     * @ORM\Column(name="bak2", type="string", length=25, nullable=true)
     */
    public ?string $car2 = null;

    /**
     * @ORM\Column(name="bak3", type="string", length=25, nullable=true)
     */
    public ?string $car3 = null;

    /**
     * @ORM\Column(name="bak4", type="string", length=25, nullable=true)
     */
    public ?string $car4 = null;

    /**
     * @ORM\Column(name="bak5", type="string", length=25, nullable=true)
     */
    public ?string $car5 = null;

    /**
     * @ORM\Column(name="bak6", type="string", length=25, nullable=true)
     */
    public ?string $car6 = null;

    /**
     * @ORM\Column(name="bak7", type="string", length=25, nullable=true)
     */
    public ?string $car7 = null;

    /**
     * @ORM\Column(name="bak8", type="string", length=25, nullable=true)
     */
    public ?string $car8 = null;

    /**
     * @ORM\Column(name="bak9", type="string", length=25, nullable=true)
     */
    public ?string $car9 = null;

    /**
     * @ORM\Column(name="bak10", type="string", length=25, nullable=true)
     */
    public ?string $car10 = null;

    /**
     * @ORM\Column(name="bak11", type="string", length=25, nullable=true)
     */
    public ?string $car11 = null;

    /**
     * @ORM\Column(name="bak12", type="string", length=25, nullable=true)
     */
    public ?string $car12 = null;

    /**
     * @ORM\Column(name="bak13", type="string", length=25, nullable=true)
     */
    public ?string $car13 = null;

    public function getCar(int $carNumber): ?string
    {
        return $this->{'car' . $carNumber};
    }
}
