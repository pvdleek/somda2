<?php

namespace App\Helpers;

use App\Entity\User;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationHelper
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private AuthorizationCheckerInterface $securityChecker;

    /**
     * @param LoggerInterface $logger
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $securityChecker
     */
    public function __construct(
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $securityChecker
    ) {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->securityChecker = $securityChecker;
    }

    /**
     * @param int $level
     * @param string $message
     */
    public function addSecurityLog($level, $message)
    {
        $this->logger->addRecord($level, $message);
    }

    /**
     * @param Request $request
     * @param array $parameters
     */
    public function checkMandatoryParameters(Request $request, array $parameters)
    {
        foreach ($parameters as $parameter) {
            if (is_null($request->get($parameter))) {
                $this->addSecurityLog(
                    Logger::ERROR,
                    'Mandatory parameter "' . $parameter . '" is missing for request'
                );
                throw new AccessDeniedHttpException();
            }
        }
    }

    /**
     * @param Request $request
     * @param array $parameters
     * @param FormInterface|null $form
     *
     * This function will verify that only the given allowed parameters are present in the request
     * If any other parameters are present, the request has been tempered with
     */
    public function checkAllowedParameters(
        Request $request,
        array $parameters,
        FormInterface $form = null
    ) {
        $parametersToVerify = array_keys(array_merge($request->request->all(), $request->query->all()));
        foreach ($parametersToVerify as $parameter) {
            if (!in_array($parameter, $parameters) && !$this->isRequestParameterAllowedInForm($parameter, $form)) {
                $this->addSecurityLog(
                    Logger::ERROR,
                    'Parameter "' . $parameter . '" is present in request but not allowed'
                );
                if (isset(debug_backtrace()[1]) && isset(debug_backtrace()[1]['function'])) {
                    $this->addSecurityLog(
                        Logger::ERROR,
                        'Calling function for not allowed parameter "' . $parameter . '": ' .
                        debug_backtrace()[1]['class'] . '::' . debug_backtrace()[1]['function']
                    );
                }
                throw new AccessDeniedHttpException();
            }
        }
    }

    /**
     * @param string $parameter
     * @param FormInterface|null $form
     * @return bool
     */
    private function isRequestParameterAllowedInForm(string $parameter, FormInterface $form = null): bool
    {
        return !is_null($form) && ($form->has($parameter) || $form->getName() === $parameter);
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
