<?php

declare(strict_types=1);

namespace App\Model;

class ForumSearchResult
{
    public bool $title_match;

    public int $discussion_id;

    public string $discussion_title;

    public bool $discussion_locked;

    public int $author_id;

    public string $author_username;

    public int $post_id;

    public \DateTime $post_timestamp;

    /**
     * @param array $queryResult - A result array from the searchByWords function in the ForumSearchWord repository
     */
    public function __construct(array $queryResult)
    {
        $this->title_match = $queryResult['title_match'];
        $this->discussion_id = (int) $queryResult['discussion_id'];
        $this->discussion_title = $queryResult['discussion_title'];
        $this->discussion_locked = (bool)$queryResult['discussion_locked'];
        $this->author_id = (int) $queryResult['author_id'];
        $this->author_username = $queryResult['author_username'];
        $this->post_id = (int) $queryResult['post_id'];
        $this->post_timestamp = $queryResult['post_timestamp'];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->discussion_id.'_'.$this->post_id;
    }
}
