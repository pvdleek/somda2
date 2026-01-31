<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\FlashHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class FlashHelperTest extends TestCase
{
    private FlashHelper $flash_helper;
    private Session $session;
    private FlashBag $flash_bag;

    protected function setUp(): void
    {
        $this->session = new Session(new MockArraySessionStorage());
        $this->flash_bag = new FlashBag();
        $this->session->registerBag($this->flash_bag);

        $request = new Request();
        $request->setSession($this->session);

        $request_stack = new RequestStack();
        $request_stack->push($request);

        $this->flash_helper = new FlashHelper($request_stack);
    }

    public function testAddInformationFlash(): void
    {
        $this->flash_helper->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Test info message');

        $messages = $this->flash_bag->get(FlashHelper::FLASH_TYPE_INFORMATION);
        $this->assertCount(1, $messages);
        $this->assertEquals('Test info message', $messages[0]);
    }

    public function testAddWarningFlash(): void
    {
        $this->flash_helper->add(FlashHelper::FLASH_TYPE_WARNING, 'Test warning message');

        $messages = $this->flash_bag->get(FlashHelper::FLASH_TYPE_WARNING);
        $this->assertCount(1, $messages);
        $this->assertEquals('Test warning message', $messages[0]);
    }

    public function testAddErrorFlash(): void
    {
        $this->flash_helper->add(FlashHelper::FLASH_TYPE_ERROR, 'Test error message');

        $messages = $this->flash_bag->get(FlashHelper::FLASH_TYPE_ERROR);
        $this->assertCount(1, $messages);
        $this->assertEquals('Test error message', $messages[0]);
    }

    public function testAddInvalidTypeDefaultsToError(): void
    {
        $this->flash_helper->add('invalid_type', 'Test message');

        $messages = $this->flash_bag->get(FlashHelper::FLASH_TYPE_ERROR);
        $this->assertCount(1, $messages);
        $this->assertEquals('Test message', $messages[0]);
    }

    public function testMultipleFlashMessages(): void
    {
        $this->flash_helper->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Info 1');
        $this->flash_helper->add(FlashHelper::FLASH_TYPE_INFORMATION, 'Info 2');
        $this->flash_helper->add(FlashHelper::FLASH_TYPE_WARNING, 'Warning 1');

        $info_messages = $this->flash_bag->get(FlashHelper::FLASH_TYPE_INFORMATION);
        $warning_messages = $this->flash_bag->get(FlashHelper::FLASH_TYPE_WARNING);

        $this->assertCount(2, $info_messages);
        $this->assertCount(1, $warning_messages);
        $this->assertEquals('Info 1', $info_messages[0]);
        $this->assertEquals('Info 2', $info_messages[1]);
        $this->assertEquals('Warning 1', $warning_messages[0]);
    }
}
