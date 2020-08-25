<?php
declare(strict_types=1);

namespace App\Model;

use DateTime;

class ForumSearchResult
{
    /**
     * @var bool
     */
    public bool $titleMatch;

    /**
     * @var int
     */
    public int $discussionId;

    /**
     * @var string
     */
    public string $discussionTitle;

    /**
     * @var bool
     */
    public bool $discussionLocked;

    /**
     * @var int
     */
    public int $authorId;

    /**
     * @var string
     */
    public string $authorUsername;

    /**
     * @var int
     */
    public int $postId;

    /**
     * @var DateTime
     */
    public DateTime $postTimestamp;

    /**
     * @param array $queryResult - A result array from the searchByWords function in the ForumSearchWord repository
     */
    public function __construct(array $queryResult)
    {
        $this->titleMatch = $queryResult['titleMatch'];
        $this->discussionId = (int)$queryResult['discussionId'];
        $this->discussionTitle = $queryResult['discussionTitle'];
        $this->discussionLocked = (bool)$queryResult['discussionLocked'];
        $this->authorId = (int)$queryResult['authorId'];
        $this->authorUsername = $queryResult['authorUsername'];
        $this->postId = (int)$queryResult['postId'];
        $this->postTimestamp = $queryResult['postTimestamp'];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->discussionId . '_' . $this->postId;
    }
}
