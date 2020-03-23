<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_patterns", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48139_volgorde", columns={"volgorde"})})
 * @ORM\Entity
 */
class TrainNamePattern
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="volgorde", type="bigint", nullable=false)
     */
    private $order;

    /**
     * @var string
     * @ORM\Column(name="pattern", type="string", length=80, nullable=false)
     */
    private $pattern;

    /**
     * @var string
     * @ORM\Column(name="naam", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(name="tekening", type="string", length=30, nullable=true)
     */
    private $image;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TrainNamePattern
     */
    public function setId(int $id): TrainNamePattern
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return TrainNamePattern
     */
    public function setOrder(int $order): TrainNamePattern
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return TrainNamePattern
     */
    public function setPattern(string $pattern): TrainNamePattern
    {
        $this->pattern = $pattern;
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
     * @return TrainNamePattern
     */
    public function setName(string $name): TrainNamePattern
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return TrainNamePattern
     */
    public function setImage(?string $image): TrainNamePattern
    {
        $this->image = $image;
        return $this;
    }
}
