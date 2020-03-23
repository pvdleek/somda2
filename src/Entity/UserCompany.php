<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_users_companies")
 * @ORM\Entity
 */
class UserCompany
{
    /**
     * @var int
     * @ORM\Column(name="bedrijf_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=15, nullable=false)
     */
    private $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserCompany
     */
    public function setId(int $id): UserCompany
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
     * @return UserCompany
     */
    public function setName(string $name): UserCompany
    {
        $this->name = $name;
        return $this;
    }
}
