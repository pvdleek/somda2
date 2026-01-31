<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\RouteOperationDays;
use App\Helpers\RouteOperationDaysHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class RouteOperationDaysHelperTest extends TestCase
{
    private RouteOperationDaysHelper $helper;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->helper = new RouteOperationDaysHelper($this->translator);
    }

    public function testGetDisplayAllDays(): void
    {
        $route_operation_days = $this->createMock(RouteOperationDays::class);
        $route_operation_days->method('isRunningOnDay')->willReturn(true);

        $this->translator
            ->method('trans')
            ->willReturnCallback(function ($key) {
                $days = [
                    'general.date.days.0' => 'maandag',
                    'general.date.days.1' => 'dinsdag',
                    'general.date.days.2' => 'woensdag',
                    'general.date.days.3' => 'donderdag',
                    'general.date.days.4' => 'vrijdag',
                    'general.date.days.5' => 'zaterdag',
                    'general.date.days.6' => 'zondag',
                ];
                return $days[$key] ?? $key;
            });

        $result = $this->helper->getDisplay($route_operation_days);

        $this->assertEquals('Maandag, dinsdag, woensdag, donderdag, vrijdag, zaterdag, zondag', $result);
    }

    public function testGetDisplayWeekdays(): void
    {
        $route_operation_days = $this->createMock(RouteOperationDays::class);
        $route_operation_days->method('isRunningOnDay')->willReturnCallback(function ($day) {
            return $day >= 0 && $day <= 4; // Monday to Friday
        });

        $this->translator
            ->method('trans')
            ->willReturnCallback(function ($key) {
                $days = [
                    'general.date.days.0' => 'maandag',
                    'general.date.days.1' => 'dinsdag',
                    'general.date.days.2' => 'woensdag',
                    'general.date.days.3' => 'donderdag',
                    'general.date.days.4' => 'vrijdag',
                ];
                return $days[$key] ?? $key;
            });

        $result = $this->helper->getDisplay($route_operation_days);

        $this->assertEquals('Maandag, dinsdag, woensdag, donderdag, vrijdag', $result);
    }

    public function testGetDisplayWeekend(): void
    {
        $route_operation_days = $this->createMock(RouteOperationDays::class);
        $route_operation_days->method('isRunningOnDay')->willReturnCallback(function ($day) {
            return $day === 5 || $day === 6; // Saturday and Sunday
        });

        $this->translator
            ->method('trans')
            ->willReturnCallback(function ($key) {
                $days = [
                    'general.date.days.5' => 'zaterdag',
                    'general.date.days.6' => 'zondag',
                ];
                return $days[$key] ?? $key;
            });

        $result = $this->helper->getDisplay($route_operation_days);

        $this->assertEquals('Zaterdag, zondag', $result);
    }

    public function testGetDisplayShortFormat(): void
    {
        $route_operation_days = $this->createMock(RouteOperationDays::class);
        $route_operation_days->method('isRunningOnDay')->willReturnCallback(function ($day) {
            return $day === 0 || $day === 1; // Monday and Tuesday
        });

        $this->translator
            ->method('trans')
            ->willReturnCallback(function ($key) {
                $days = [
                    'general.date.daysShort.0' => 'ma',
                    'general.date.daysShort.1' => 'di',
                ];
                return $days[$key] ?? $key;
            });

        $result = $this->helper->getDisplay($route_operation_days, true);

        $this->assertEquals('Ma, di', $result);
    }

    public function testGetDisplaySingleDay(): void
    {
        $route_operation_days = $this->createMock(RouteOperationDays::class);
        $route_operation_days->method('isRunningOnDay')->willReturnCallback(function ($day) {
            return $day === 3; // Only Thursday
        });

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('general.date.days.3')
            ->willReturn('donderdag');

        $result = $this->helper->getDisplay($route_operation_days);

        $this->assertEquals('Donderdag', $result);
    }

    public function testGetDisplayNoDays(): void
    {
        $route_operation_days = $this->createMock(RouteOperationDays::class);
        $route_operation_days->method('isRunningOnDay')->willReturn(false);

        $this->translator
            ->expects($this->never())
            ->method('trans');

        $result = $this->helper->getDisplay($route_operation_days);

        $this->assertEquals('', $result);
    }
}
