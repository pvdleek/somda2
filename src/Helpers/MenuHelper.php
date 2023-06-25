<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Block;
use App\Entity\ForumPostAlert;
use App\Generics\RoleGenerics;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\RuntimeExtensionInterface;

class MenuHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly AuthorizationHelper $authorizationHelper,
    ) {
    }

    public function getNumberOfOpenForumAlerts(): int
    {
        if ($this->authorizationHelper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return $this->doctrine->getRepository(ForumPostAlert::class)->getNumberOfOpenAlerts();
        }
        return 0;
    }

    public function getMenuStructure(): array
    {
        $blocks = $this->doctrine->getRepository(Block::class)->getMenuStructure();
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
        if (\is_null($block['role']) || $this->authorizationHelper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return true;
        }
        return \substr($block['role'], 0, 10) !== 'ROLE_ADMIN' || $this->authorizationHelper->isGranted($block['role']);
    }
}
