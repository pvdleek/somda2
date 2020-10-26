<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fsl_forum_search_list", indexes={
 *     @ORM\Index(name="IDX_fsl_fsw_id", columns={"fsl_fsw_id"}),
 *     @ORM\Index(name="IDX_fsl_fop_id", columns={"fsl_fop_id"}),
 * })
 * @ORM\Entity
 */
class ForumSearchList
{
    /**
     * @var ForumSearchWord
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumSearchWord", inversedBy="lists")
     * @ORM\JoinColumn(name="fsl_fsw_id", referencedColumnName="fsw_id")
     * @ORM\Id
     */
    public ForumSearchWord $word;

    /**
     * @var ForumPost
     * @ORM\ManyToOne(targetEntity="App\Entity\ForumPost", inversedBy="searchLists")
     * @ORM\JoinColumn(name="fsl_fop_id", referencedColumnName="fop_id")
     * @ORM\Id
     */
    public ForumPost $post;

    /**
     * @var bool
     * @ORM\Column(name="fsl_in_title", type="boolean", nullable=false)
     */
    public bool $title = false;
}
