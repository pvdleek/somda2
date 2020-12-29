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
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var AuthorizationHelper
     */
    private AuthorizationHelper $authorizationHelper;

    /**
     * @param ManagerRegistry $doctrine
     * @param AuthorizationHelper $authorizationHelper
     */
    public function __construct(ManagerRegistry $doctrine, AuthorizationHelper $authorizationHelper)
    {
        $this->doctrine = $doctrine;
        $this->authorizationHelper = $authorizationHelper;
    }

    /**
     * @return int
     */
    public function getNumberOfOpenForumAlerts(): int
    {
        if ($this->authorizationHelper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return $this->doctrine->getRepository(ForumPostAlert::class)->getNumberOfOpenAlerts();
        }
        return 0;
    }

    /**
     * @return array
     */
    public function getMenuStructure(): array
    {
        $blocks = $this->doctrine->getRepository(Block::class)->getMenuStructure();
        $allowedBlocks = [];

        foreach ($blocks as $block) {
            if (strlen($block['route']) > 0 && $this->isAuthorizedForBlock($block)) {
                $allowedBlocks[] = $block;
            }
        }

        return $allowedBlocks;
    }

    /**
     * @param array $block
     * @return bool
     */
    private function isAuthorizedForBlock(array $block): bool
    {
        if ($block['role'] === 'IS_AUTHENTICATED_ANONYMOUSLY') {
            return is_null($this->authorizationHelper->getUser());
        }
        if (is_null($block['role']) || $this->authorizationHelper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return true;
        }
        return substr($block['role'], 0, 10) !== 'ROLE_ADMIN' || $this->authorizationHelper->isGranted($block['role']);
    }
}
