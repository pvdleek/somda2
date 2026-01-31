<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\User;
use App\Generics\RoleGenerics;
use App\Helpers\AuthorizationHelper;
use App\Helpers\MenuHelper;
use App\Repository\BlockRepository;
use App\Repository\ForumPostAlertRepository;
use PHPUnit\Framework\TestCase;

class MenuHelperTest extends TestCase
{
    private MenuHelper $menu_helper;
    private AuthorizationHelper $authorization_helper;
    private ForumPostAlertRepository $forum_post_alert_repository;
    private BlockRepository $block_repository;

    protected function setUp(): void
    {
        $this->authorization_helper = $this->createMock(AuthorizationHelper::class);
        $this->forum_post_alert_repository = $this->createMock(ForumPostAlertRepository::class);
        $this->block_repository = $this->createMock(BlockRepository::class);

        $this->menu_helper = new MenuHelper(
            $this->authorization_helper,
            $this->forum_post_alert_repository,
            $this->block_repository
        );
    }

    public function testGetNumberOfOpenForumAlertsForAdmin(): void
    {
        $this->authorization_helper
            ->expects($this->once())
            ->method('isGranted')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(true);

        $this->forum_post_alert_repository
            ->expects($this->once())
            ->method('getNumberOfOpenAlerts')
            ->willReturn(5);

        $result = $this->menu_helper->getNumberOfOpenForumAlerts();

        $this->assertEquals(5, $result);
    }

    public function testGetNumberOfOpenForumAlertsForNonAdmin(): void
    {
        $this->authorization_helper
            ->expects($this->once())
            ->method('isGranted')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(false);

        $this->forum_post_alert_repository
            ->expects($this->never())
            ->method('getNumberOfOpenAlerts');

        $result = $this->menu_helper->getNumberOfOpenForumAlerts();

        $this->assertEquals(0, $result);
    }

    public function testGetMenuStructureFiltersEmptyRoutes(): void
    {
        $blocks = [
            ['route' => 'home', 'role' => null],
            ['route' => '', 'role' => null],
            ['route' => 'admin', 'role' => RoleGenerics::ROLE_ADMIN],
        ];

        $this->block_repository
            ->expects($this->once())
            ->method('getMenuStructure')
            ->willReturn($blocks);

        $this->authorization_helper
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturn(false);

        $result = $this->menu_helper->getMenuStructure();

        $this->assertCount(1, $result);
        $this->assertEquals('home', $result[0]['route']);
    }

    public function testGetMenuStructureForAdmin(): void
    {
        $blocks = [
            ['route' => 'home', 'role' => null],
            ['route' => 'admin', 'role' => RoleGenerics::ROLE_ADMIN],
        ];

        $this->block_repository
            ->expects($this->once())
            ->method('getMenuStructure')
            ->willReturn($blocks);

        $this->authorization_helper
            ->method('isGranted')
            ->with(RoleGenerics::ROLE_ADMIN)
            ->willReturn(true);

        $result = $this->menu_helper->getMenuStructure();

        $this->assertCount(2, $result);
    }

    public function testGetMenuStructureFiltersUnauthorizedBlocks(): void
    {
        $blocks = [
            ['route' => 'home', 'role' => null],
            ['route' => 'admin', 'role' => 'ROLE_ADMIN_SPECIAL'],
            ['route' => 'user', 'role' => 'ROLE_USER'],
        ];

        $this->block_repository
            ->expects($this->once())
            ->method('getMenuStructure')
            ->willReturn($blocks);

        $this->authorization_helper
            ->method('isGranted')
            ->willReturnCallback(function ($role) {
                return $role === 'ROLE_USER';
            });

        $result = $this->menu_helper->getMenuStructure();

        // Should include 'home' (no role required) and 'user' (granted)
        $this->assertCount(2, $result);
    }
}
