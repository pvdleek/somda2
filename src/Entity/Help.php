<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_help")
 * @ORM\Entity
 */
class Help extends Entity
{
    /**
     * @var int|null
     * @ORM\Column(name="contentid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="titel", type="text", length=255, nullable=false)
     */
    public string $title = '';

    /**
     * @var string
     * @ORM\Column(name="template", type="text", length=255, nullable=false)
     */
    public string $template = '';
}
