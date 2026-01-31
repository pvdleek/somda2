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
     * @param array $query_result - A result array from the searchByWords function in the ForumSearchWord repository
     */
    public function __construct(array $query_result)
    {
        $this->title_match = $query_result['title_match'];
        $this->discussion_id = (int) $query_result['discussion_id'];
        $this->discussion_title = $query_result['discussion_title'];
        $this->discussion_locked = (bool)$query_result['discussion_locked'];
        $this->author_id = (int) $query_result['author_id'];
        $this->author_username = $query_result['author_username'];
        $this->post_id = (int) $query_result['post_id'];
        $this->post_timestamp = $query_result['post_timestamp'];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->discussion_id.'_'.$this->post_id;
    }
}
