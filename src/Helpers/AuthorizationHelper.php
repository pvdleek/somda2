<?php

namespace App\Helpers;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationHelper
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private AuthorizationCheckerInterface $securityChecker;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $securityChecker
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $securityChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->securityChecker = $securityChecker;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }
        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function isGranted(string $role): bool
    {
        return $this->securityChecker->isGranted($role, $this->getUser());
    }
}
