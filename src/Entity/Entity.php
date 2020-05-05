<?php

namespace App\Entity;

abstract class Entity
{
    /**
     * @var int
     */
    protected ?int $id = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return static
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
}
