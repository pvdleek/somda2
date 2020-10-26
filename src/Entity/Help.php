<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hel_help")
 * @ORM\Entity
 */
class Help
{
    /**
     * @var int|null
     * @ORM\Column(name="hel_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="hel_title", type="text", length=255, nullable=false)
     */
    public string $title = '';

    /**
     * @var string
     * @ORM\Column(name="hel_template", type="text", length=255, nullable=false)
     */
    public string $template = '';
}
