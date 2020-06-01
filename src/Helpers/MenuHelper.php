<?php

namespace App\Helpers;

use App\Entity\Block;
use App\Entity\ForumPostAlert;
use App\Entity\Position;
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
        if ($this->authorizationHelper->getUser()
            && $this->authorizationHelper->getUser()->hasRole(RoleGenerics::ROLE_ADMIN)
        ) {
            $openAlerts = $this->doctrine->getRepository(ForumPostAlert::class)->findBy(['closed' => false]);
            return count($openAlerts);
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
        if (is_null($block['role']) || $this->authorizationHelper->isGranted(RoleGenerics::ROLE_ADMIN)) {
            return true;
        }
        if ($block['role'] === 'IS_AUTHENTICATED_ANONYMOUSLY') {
            return is_null($this->authorizationHelper->getUser());
        }
        return substr($block['role'], 0, 11) !== 'ROLE_ADMIN_' || $this->authorizationHelper->isGranted($block['role']);
    }
}
