<?php

namespace App\Helpers;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationHelper
{
    public function __construct(
        private readonly TokenStorageInterface $token_storage,
        private readonly AuthorizationCheckerInterface $security_checker,
    ) {
    }

    public function getUser(): ?User
    {
        if (null === $token = $this->token_storage->getToken()) {
            return null;
        }
        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        /** @var User $user */
        return $user;
    }

    public function isGranted(string $role): bool
    {
        return $this->security_checker->isGranted($role, $this->getUser());
    }
}
