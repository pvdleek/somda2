<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SpecialRouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialRouteRepository::class)]
#[ORM\Table(name: 'somda_drgl')]
class SpecialRoute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'drglid', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(name: 'pubdatum', type: 'datetime', nullable: true)]
    public ?\DateTime $publication_timestamp = null;

    #[ORM\Column(name: 'datum', type: 'date', nullable: true)]
    public ?\DateTime $start_date = null;

    #[ORM\Column(name: 'einddatum', type: 'date', nullable: true)]
    public ?\DateTime $end_date = null;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    public bool $public = false;

    #[ORM\Column(length: 75, nullable: false, options: ['default' => ''])]
    public string $title = '';

    #[ORM\Column(length: 20, nullable: false, options: ['default' => ''])]
    public string $image = '';

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    public string $text = '';

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'somda_drgl_read', 
        joinColumns: [new ORM\JoinColumn(name: 'drglid', referencedColumnName: 'drglid')], 
        inverseJoinColumns: [new ORM\JoinColumn(name: 'uid', referencedColumnName: 'uid')]
    )]
    private Collection $user_reads;

    public function __construct()
    {
        $this->user_reads = new ArrayCollection();
    }

    public function addUserRead(User $user): SpecialRoute
    {
        $this->user_reads[] = $user;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getUserReads(): array
    {
        return $this->user_reads->toArray();
    }
}
