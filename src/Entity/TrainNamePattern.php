<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="tnp_train_name_pattern",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_tnp_order", columns={"tnp_order"})}
 * )
 * @ORM\Entity
 */
class TrainNamePattern
{
    /**
     * @var int|null
     * @ORM\Column(name="tnp_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var int
     * @ORM\Column(name="tnp_order", type="integer", nullable=false, options={"default"="1"})
     */
    public int $order = 1;

    /**
     * @var string
     * @ORM\Column(name="tnp_pattern", type="string", length=80, nullable=false, options={"default"=""})
     */
    public string $pattern = '';

    /**
     * @var string
     * @ORM\Column(name="tnp_name", type="string", length=50, nullable=false, options={"default"=""})
     */
    public string $name = '';

    /**
     * @var string|null
     * @ORM\Column(name="tnp_image", type="string", length=30, nullable=true)
     */
    public ?string $image;
}
