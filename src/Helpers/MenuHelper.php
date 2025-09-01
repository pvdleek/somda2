<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Generics\RoleGenerics;
use App\Repository\BlockRepository;
use App\Repository\ForumPostAlertRepository;
use Twig\Extension\RuntimeExtensionInterface;

class MenuHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly AuthorizationHelper $authorization_helper,
        private readonly ForumPostAlertRepository $forum_post_alert_repository,
        private readonly BlockRepository $block_repository,
    ) {
    }

    public function getNumberOfOpenForumAlerts(): int
    {
        if ($this->authorization_helper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return $this->forum_post_alert_repository->getNumberOfOpenAlerts();
        }
        return 0;
    }

    public function getMenuStructure(): array
    {
        $blocks = $this->block_repository->getMenuStructure();
        $allowedBlocks = [];

        foreach ($blocks as $block) {
            if (\strlen($block['route']) > 0 && $this->isAuthorizedForBlock($block)) {
                $allowedBlocks[] = $block;
            }
        }

        return $allowedBlocks;
    }

    private function isAuthorizedForBlock(array $block): bool
    {
        if (null === $block['role'] || $this->authorization_helper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return true;
        }
        return \substr($block['role'], 0, 10) !== 'ROLE_ADMIN' || $this->authorization_helper->isGranted($block['role']);
    }
}
