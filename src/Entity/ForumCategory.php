<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="foc_forum_category")
 * @ORM\Entity
 */
class ForumCategory
{
    /**
     * @var int|null
     * @ORM\Column(name="foc_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose()
     * @SWG\Property(description="Unique identifier", type="integer")
     */
    public ?int $id = null;

    /**
     * @var string
     * @ORM\Column(name="foc_name", type="string", length=30, nullable=false)
     * @JMS\Expose()
     * @SWG\Property(description="Name of the category", maxLength=30, type="string")
     */
    public string $name;

    /**
     * @var int
     * @ORM\Column(name="foc_order", type="integer", nullable=false, options={"default"="1"})
     * @JMS\Expose()
     * @SWG\Property(description="The order in which to display the forums", type="integer")
     */
    public int $order = 1;

    /**
     * @var ForumForum[]
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
