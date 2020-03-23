<?php

namespace App\Helpers;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BreadcrumbHelper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $parts = [];

    /**
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param string $title
     * @param string $route
     * @param array $routeArguments
     * @param bool $lastPart
     */
    public function addPart($title, $route, array $routeArguments = [], $lastPart = false)
    {
        $this->parts[] = [
            'title' => $this->translator->trans($title),
            'route' => $this->router->generate($route, $routeArguments),
            'lastPart' => $lastPart
        ];
    }

    /**
     * @return string
     */
    public function getBreadcrumb(): string
    {
        if (count($this->parts) < 1) {
            return '';
        }
        $out = '<span class="segment_path"><a href="' . $this->router->generate('home') . '">';
        $out .= $this->translator->trans('general.navigation.home') . '</a>';

        foreach ($this->parts as $part) {
            $out .= ' &gt; <a href="' . $part['route'] . '" class="segment_path">';
            $out .= ($part['lastPart'] ? '<h1>' . $part['title'] . '</h1>' : $part['title']) . '</a>';
        }

        return $out;
    }
}
