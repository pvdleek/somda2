<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RedirectHelper
{
    protected const REDIRECT_STATUS = 302;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $url
     * @param int $status
     * @return RedirectResponse
     */
    public function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @param string $route
     * @param array $parameters
     * @return RedirectResponse
     */
    public function redirectToRoute(string $route, array $parameters = []): RedirectResponse
    {
        return $this->redirect(
            $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH),
            self::REDIRECT_STATUS
        );
    }
}
