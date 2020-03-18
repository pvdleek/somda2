<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_jargon")
 * @ORM\Entity
 */
class Jargon
{
    /**
     * @var int
     * @ORM\Column(name="jargonid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="term", type="string", length=15, nullable=false)
     */
    private $term = '';

    /**
     * @var string
     * @ORM\Column(name="image", type="string", length=20, nullable=false)
     */
    private $image = '';

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=150, nullable=false)
     */
    private $description = '';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Jargon
     */
    public function setId(int $id): Jargon
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @param string $term
     * @return Jargon
     */
    public function setTerm(string $term): Jargon
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Jargon
     */
    public function setImage(string $image): Jargon
    {
        $this->image = $image;
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
     * @return Jargon
     */
    public function setDescription(string $description): Jargon
    {
        $this->description = $description;
        return $this;
    }
}
