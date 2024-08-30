<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Generics\RoleGenerics;
use App\Repository\Block;
use App\Repository\ForumPostAlert;
use Twig\Extension\RuntimeExtensionInterface;

class MenuHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly AuthorizationHelper $authorizationHelper,
        private readonly ForumPostAlert $repositoryForumPostAlert,
        private readonly Block $repositoryBlock,
    ) {
    }

    public function getNumberOfOpenForumAlerts(): int
    {
        if ($this->authorizationHelper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return $this->repositoryForumPostAlert->getNumberOfOpenAlerts();
        }
        return 0;
    }

    public function getMenuStructure(): array
    {
        $blocks = $this->repositoryBlock->getMenuStructure();
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
        if (null === $block['role'] || $this->authorizationHelper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return true;
        }
        return \substr($block['role'], 0, 10) !== 'ROLE_ADMIN' || $this->authorizationHelper->isGranted($block['role']);
    }
}
