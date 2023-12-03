<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Table(name="somda_forum_cats")
 * @ORM\Entity
 */
class ForumCategory
{
    /**
     * @ORM\Column(name="catid", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @OA\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     * @JMS\Expose()
     * @OA\Property(description="Name of the category", maxLength=30, type="string")
     */
    public string $name = '';

    /**
     * @ORM\Column(name="volgorde", type="smallint", nullable=false, options={"default"="1", "unsigned"=true})
     * @JMS\Expose()
     * @OA\Property(description="The order in which to display the forums", type="integer")
     */
    public int $order = 1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ForumForum", mappedBy="category")
     * @JMS\Exclude()
     */
    private $forums;

    /**
     *
     */
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
