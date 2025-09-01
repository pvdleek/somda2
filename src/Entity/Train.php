<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: TrainRepository::class)]
#[ORM\Table(
    name: 'somda_mat',
    uniqueConstraints: [new ORM\UniqueConstraint(name: 'unq_somda_mat__nummer', columns: ['nummer'])],
    indexes: [new ORM\Index(name: 'idx_somda_mat__vervoerder_id', columns: ['vervoerder_id'])]
)]
class Train
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'matid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Number of the train", maxLength=20, type="string")
     */
    #[ORM\Column(name: 'nummer', length: 20, nullable: false, options: ['default' => ''])]
    public string $number = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="Name of the train if known", maxLength=50, type="string")
     */
    #[ORM\Column(name: 'naam', length: 50, nullable: true)]
    public ?string $name = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="The transporter of this train if known", ref=@Model(type=Transporter::class))
     */
    #[ORM\ManyToOne(targetEntity: Transporter::class, inversedBy: 'trains')]
    #[ORM\JoinColumn(name: 'vervoerder_id', referencedColumnName: 'vervoerder_id')]
    public ?Transporter $transporter = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\ManyToOne(targetEntity: TrainNamePattern::class)]
    #[ORM\JoinColumn(name: 'pattern_id', referencedColumnName: 'id')]
    public ?TrainNamePattern $name_pattern = null;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: Spot::class, mappedBy: 'train')]
    private Collection $spots;

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
