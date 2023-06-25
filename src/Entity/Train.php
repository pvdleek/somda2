<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(
 *     name="somda_mat",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_48117_nummer", columns={"nummer"})},
 *     indexes={@ORM\Index(name="idx_48117_vervoerder_id", columns={"vervoerder_id"})}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\Train")
 */
class Train
{
    /**
     * @ORM\Column(name="matid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="nummer", type="string", length=20, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Number of the train", maxLength=20, type="string")
     */
    public string $number = '';

    /**
     * @ORM\Column(name="naam", type="string", length=50, nullable=true)
     * @JMS\Expose()
     * @OA\Property(description="Name of the train if known", maxLength=50, type="string")
     */
    public ?string $name = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="vervoerder_id", referencedColumnName="vervoerder_id")
     * @JMS\Expose()
     * @OA\Property(description="The transporter of this train if known", ref=@Model(type=Transporter::class))
     */
    public ?Transporter $transporter = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNamePattern")
     * @ORM\JoinColumn(name="pattern_id", referencedColumnName="id")
     * @JMS\Exclude()
     */
    public ?TrainNamePattern $namePattern = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="train")
     * @JMS\Exclude()
     */
    private $spots;

    public function __construct()
    {
        $this->spots = new ArrayCollection();
    }

    public function addSpot(Spot $spot): Train
    {
        $this->spots[] = $spot;
        return $this;
    }

    /**
     * @return Spot[]
     */
    public function getSpots(): array
    {
        return $this->spots->toArray();
    }
}
