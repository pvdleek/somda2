<?php

namespace App\Helpers;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationHelper
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AuthorizationCheckerInterface $securityChecker,
    ) {
    }

    public function getUser(): ?User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }
        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        /**
         * @var User $user
         */
        return $user;
    }

    public function isGranted(string $role): bool
    {
        return $this->securityChecker->isGranted($role, $this->getUser());
    }
}
