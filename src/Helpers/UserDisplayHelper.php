<?php

namespace App\Helpers;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class UserDisplayHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
    ) {
    }

    public function getDisplayUser(int $id, string $username): string
    {
        if ($id < 0) {
            return $username;
        }
        $display_user = '<a href="'.$this->router->generate('profile_view', ['id' => $id]).'"';
        $display_user .= ' title="'.\sprintf($this->translator->trans('profile.view.title'), $username).'">';

        return $display_user.$username.'</a>';
    }
}
