<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_groups")
 * @ORM\Entity
 */
class Group
{
    /**
     * @var int
     * @ORM\Column(name="groupid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=15, nullable=false)
     */
    private $name = '';

    /**
     * @var User
     * @ORM\ManyToMany(targetEntity="User", inversedBy="groups")
     * @ORM\JoinTable(name="somda_users_groups",
     *      joinColumns={@ORM\JoinColumn(name="groupid", referencedColumnName="groupid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="uid", referencedColumnName="uid")}
     * )
     */
    private $users;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Group
     */
    public function setId(int $id): Group
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Group
     */
    public function setName(string $name): Group
    {
        $this->name = $name;
        return $this;
    }
}
