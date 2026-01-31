<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\UserDisplayHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserDisplayHelperTest extends TestCase
{
    private UserDisplayHelper $user_display_helper;
    private TranslatorInterface $translator;
    private RouterInterface $router;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->user_display_helper = new UserDisplayHelper($this->translator, $this->router);
    }

    public function testGetDisplayUserWithNegativeId(): void
    {
        $result = $this->user_display_helper->getDisplayUser(-1, 'DeletedUser');

        $this->assertEquals('DeletedUser', $result);
        $this->assertStringNotContainsString('<a', $result);
    }

    public function testGetDisplayUserWithPositiveId(): void
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('profile_view', ['id' => 123])
            ->willReturn('/profile/123');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('profile.view.title')
            ->willReturn('View profile of %s');

        $result = $this->user_display_helper->getDisplayUser(123, 'TestUser');

        $this->assertStringContainsString('<a href="/profile/123"', $result);
        $this->assertStringContainsString('title="View profile of TestUser"', $result);
        $this->assertStringContainsString('TestUser</a>', $result);
    }

    public function testGetDisplayUserWithZeroId(): void
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('profile_view', ['id' => 0])
            ->willReturn('/profile/0');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('profile.view.title')
            ->willReturn('View profile of %s');

        $result = $this->user_display_helper->getDisplayUser(0, 'SystemUser');

        $this->assertStringContainsString('<a href="/profile/0"', $result);
        $this->assertStringContainsString('SystemUser</a>', $result);
    }

    public function testGetDisplayUserWithSpecialCharacters(): void
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('profile_view', ['id' => 456])
            ->willReturn('/profile/456');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('profile.view.title')
            ->willReturn('View profile of %s');

        $result = $this->user_display_helper->getDisplayUser(456, 'User&Name<>');

        $this->assertStringContainsString('User&Name<>', $result);
    }
}
