<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticator extends BaseFormAuthenticator
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($entityManager, $urlGenerator, $csrfTokenManager);

        $this->setLoginRoute('login')->setHomeRoute('home');
    }
}
