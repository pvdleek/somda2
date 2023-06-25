<?php

namespace App\Feed;

use App\Entity\User;
use App\Exception\UnusedItemAuthorMethod;
use FeedIo\Feed\Item\AuthorInterface;

class ItemAuthor implements AuthorInterface
{
    private const SETTER_EXCEPTION_MESSAGE = 'Use the ItemAuthor constructor to set the author';

    public function __construct(
        private readonly User $user,
    ) {
    }

    public function getEmail(): ?string
    {
        return 'webmaster@somda.nl';
    }

    public function getName(): ?string
    {
        return $this->user->name ?? $this->user->username;
    }

    public function getUri(): ?string
    {
        return null;
    }

    /**
     * @throws UnusedItemAuthorMethod
     */
    public function setEmail(string $email = null): AuthorInterface
    {
        throw new UnusedItemAuthorMethod(self::SETTER_EXCEPTION_MESSAGE);
    }

    /**
     * @throws UnusedItemAuthorMethod
     */
    public function setName(string $name = null): AuthorInterface
    {
        throw new UnusedItemAuthorMethod(self::SETTER_EXCEPTION_MESSAGE);
    }

    /**
     * @throws UnusedItemAuthorMethod
     */
    public function setUri(string $uri = null): AuthorInterface
    {
        throw new UnusedItemAuthorMethod(self::SETTER_EXCEPTION_MESSAGE);
    }
}
