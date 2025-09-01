<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'somda_groups')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'groupid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    #[ORM\Column(length: 15, nullable: false, options: ['default' => ''])]
    public string $name = '';

    #[ORM\Column(type: 'array', nullable: false, options: ['default' => []])]
    public array $roles = [];

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups')]
    #[ORM\JoinTable(name: 'somda_users_groups')]
    #[ORM\JoinColumn(name: 'groupid', referencedColumnName: 'groupid')]
    #[ORM\InverseJoinColumn(name: 'uid', referencedColumnName: 'uid')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function addUser(User $user): Group
    {
        $this->users[] = $user;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }
}
