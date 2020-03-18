<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_positie")
 * @ORM\Entity
 */
class Position
{
    /**
     * @var int
     * @ORM\Column(name="posid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="positie", type="string", length=2, nullable=false)
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
     * @return Position
     */
    public function setId(int $id): Position
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
     * @return Position
     */
    public function setName(string $name): Position
    {
        $this->name = $name;
        return $this;
    }
}
