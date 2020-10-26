<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(
 *     name="tra_train",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_tra_number", columns={"tra_number"})},
 *     indexes={
 *         @ORM\Index(name="IDX_tra_trn_id", columns={"tra_trn_id"}),
 *         @ORM\Index(name="IDX_tra_tnp_id", columns={"tra_tnp_id"}),
 *     }
 * )
 * @ORM\Entity
 */
class Train
{
    /**
     * @var int|null
     * @ORM\Column(name="tra_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="tra_number", type="string", length=20, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Number of the train", maxLength=20, type="string")
     */
    public string $number = '';

    /**
     * @var string|null
     * @ORM\Column(name="tra_name", type="string", length=35, nullable=true)
     * @JMS\Expose()
     * @SWG\Property(description="Name of the train if known", maxLength=35, type="string")
     */
    public ?string $name;

    /**
     * @var Transporter|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Transporter")
     * @ORM\JoinColumn(name="tra_trn_id", referencedColumnName="trn_id")
     * @JMS\Expose()
     * @SWG\Property(description="The transporter of this train if known", ref=@Model(type=Transporter::class))
     */
    public ?Transporter $transporter;

    /**
     * @var TrainNamePattern|null
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainNamePattern")
     * @ORM\JoinColumn(name="tra_tnp_id", referencedColumnName="tnp_id")
     * @JMS\Exclude()
     */
    public ?TrainNamePattern $namePattern = null;

    /**
     * @var Spot[]
     * @ORM\OneToMany(targetEntity="App\Entity\Spot", mappedBy="train")
     * @JMS\Exclude()
     */
    private $spots;

    /**
     *
     */
    public function __construct()
    {
        $this->spots = new ArrayCollection();
    }

    /**
     * @param Spot $spot
     * @return Train
     */
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
