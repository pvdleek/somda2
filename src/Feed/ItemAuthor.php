<?php

namespace App\Feed;

use App\Entity\User;
use App\Exception\UnusedItemAuthorMethod;
use FeedIo\Feed\Item\AuthorInterface;

class ItemAuthor implements AuthorInterface
{
    private const SETTER_EXCEPTION_MESSAGE = 'Use the ItemAuthor constructor to set the author';

    /**
     * @var User
     */
    private User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return 'webmaster@somda.nl';
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->user->name ?? $this->user->username;
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return null;
    }

    /**
     * @param string|null $email
     * @return AuthorInterface
     * @throws UnusedItemAuthorMethod
     */
    public function setEmail(string $email = null): AuthorInterface
    {
        throw new UnusedItemAuthorMethod(self::SETTER_EXCEPTION_MESSAGE);
    }

    /**
     * @param string|null $name
     * @return AuthorInterface
     * @throws UnusedItemAuthorMethod
     */
    public function setName(string $name = null): AuthorInterface
    {
        throw new UnusedItemAuthorMethod(self::SETTER_EXCEPTION_MESSAGE);
    }

    /**
     * @param string|null $uri
     * @return AuthorInterface
     * @throws UnusedItemAuthorMethod
     */
    public function setUri(string $uri = null): AuthorInterface
    {
        throw new UnusedItemAuthorMethod(self::SETTER_EXCEPTION_MESSAGE);
    }
}
