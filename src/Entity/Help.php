<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="somda_help")
 * @ORM\Entity
 */
class Help
{
    /**
     * @ORM\Column(name="contentid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="titel", type="text", length=255, nullable=false)
     */
    public string $title = '';

    /**
     * @ORM\Column(name="template", type="text", length=255, nullable=false)
     */
    public string $template = '';
}
