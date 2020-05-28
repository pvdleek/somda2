<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MobileLoginFormAuthenticator extends BaseFormAuthenticator
{
    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($entityManager, $urlGenerator, $csrfTokenManager);

        $this->setLoginRoute('mobile_login')->setHomeRoute('mobile_home');
    }
}
