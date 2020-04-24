<?php

namespace App\Helpers;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class UserDisplayHelper implements RuntimeExtensionInterface
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
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @param int $id
     * @param string $username
     * @return string
     */
    public function getDisplayUser(int $id, string $username): string
    {
        if ($id < 0) {
            return $username;
        }
        $displayUser = '<a href="' . $this->router->generate('profile_view', ['id' => $id]) . '"';
        $displayUser .= ' title="' . sprintf($this->translator->trans('profile.view.title'), $username) . '">';
        return $displayUser . $username . '</a>';
    }
}
