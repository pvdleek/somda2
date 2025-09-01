<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

#[ORM\Entity]
#[ORM\Table(name: 'somda_forum_cats')]
class ForumCategory
{
    /**
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'catid', type: 'smallint', nullable: false, options: ['unsigned' => true])]
    public ?int $id = null;

    /**
     * @JMS\Expose()
     * @OA\Property(description="Name of the category", maxLength=30, type="string")
     */
    #[ORM\Column(length: 30, nullable: false, options: ['default' => ''])]
    public string $name = '';

    /**
     * @JMS\Expose()
     * @OA\Property(description="The order in which to display the forums", type="integer")
     */
    #[ORM\Column(name: 'volgorde', type: 'smallint', nullable: false, options: ['default' => 1, 'unsigned' => true])]
    public int $order = 1;

    /**
     * @JMS\Exclude()
     */
    #[ORM\OneToMany(targetEntity: ForumForum::class, mappedBy: 'category')]
    private Collection $forums;

    public function __construct()
    {
        $this->forums = new ArrayCollection();
    }

    /**
     * @param ForumForum $forum
     * @return ForumCategory
     */
    public function addForum(ForumForum $forum): ForumCategory
    {
        $this->forums[] = $forum;
        return $this;
    }

    /**
     * @return ForumForum[]
     */
    public function getForums(): array
    {
        return $this->forums->toArray();
    }
}
