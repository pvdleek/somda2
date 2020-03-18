<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_mat_type_patterns", uniqueConstraints={@ORM\UniqueConstraint(name="idx_48162_mattype", columns={"mattype"})})
 * @ORM\Entity
 */
class TrainTypeNamePattern
{
    /**
     * @var int
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="mattype", type="string", length=6, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="omschrijving", type="string", length=32, nullable=false)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="pattern_id_list", type="string", length=100, nullable=false)
     */
    private $patternIdList;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TrainTypeNamePattern
     */
    public function setId(int $id): TrainTypeNamePattern
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
     * @return TrainTypeNamePattern
     */
    public function setName(string $name): TrainTypeNamePattern
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
     * @return TrainTypeNamePattern
     */
    public function setDescription(string $description): TrainTypeNamePattern
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getPatternIdList(): string
    {
        return $this->patternIdList;
    }

    /**
     * @param string $patternIdList
     * @return TrainTypeNamePattern
     */
    public function setPatternIdList(string $patternIdList): TrainTypeNamePattern
    {
        $this->patternIdList = $patternIdList;
        return $this;
    }
}
