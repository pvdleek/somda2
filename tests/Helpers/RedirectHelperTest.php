<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\RedirectHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RedirectHelperTest extends TestCase
{
    private RedirectHelper $redirect_helper;
    private RouterInterface $router;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->redirect_helper = new RedirectHelper($this->router);
    }

    public function testRedirectWithDefaultStatus(): void
    {
        $response = $this->redirect_helper->redirect('https://example.com');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('https://example.com', $response->getTargetUrl());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testRedirectWithCustomStatus(): void
    {
        $response = $this->redirect_helper->redirect('https://example.com', 301);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('https://example.com', $response->getTargetUrl());
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testRedirectToRouteWithoutParameters(): void
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('home', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/home');

        $response = $this->redirect_helper->redirectToRoute('home');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/home', $response->getTargetUrl());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testRedirectToRouteWithParameters(): void
    {
        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('user_profile', ['id' => 123], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/user/123');

        $response = $this->redirect_helper->redirectToRoute('user_profile', ['id' => 123]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/user/123', $response->getTargetUrl());
        $this->assertEquals(302, $response->getStatusCode());
    }
}
