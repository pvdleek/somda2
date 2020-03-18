<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_karakteristiek", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48102_omschrijving", columns={"naam"})})
 * @ORM\Entity
 */
class Characteristic
{
    /**
     * @var int
     * @ORM\Column(name="karakteristiek_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=5, nullable=false)
     */
    private $name = '';

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=25, nullable=false)
     */
    private $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Characteristic
     */
    public function setId(int $id): Characteristic
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
     * @return Characteristic
     */
    public function setName(string $name): Characteristic
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Characteristic
     */
    public function setDescription(string $description): Characteristic
    {
        $this->description = $description;
        return $this;
    }
}
