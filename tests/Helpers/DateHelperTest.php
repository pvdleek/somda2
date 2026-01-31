<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\DateHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateHelperTest extends TestCase
{
    private DateHelper $date_helper;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->date_helper = new DateHelper($this->translator);
    }

    public function testGetDisplayDateWithNull(): void
    {
        $result = $this->date_helper->getDisplayDate(null);
        $this->assertEquals('', $result);
    }

    public function testGetDisplayDateWithDateTime(): void
    {
        $date = new \DateTime('2024-03-15 14:30:00');

        $this->translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->willReturnCallback(function ($key) {
                if ($key === 'general.date.days.4') {
                    return 'vrijdag';
                }
                if ($key === 'general.date.months.3') {
                    return 'maart';
                }
                return $key;
            });

        $result = $this->date_helper->getDisplayDate($date);

        $this->assertEquals('vrijdag 15 maart 2024', $result);
    }

    public function testGetDisplayDateWithString(): void
    {
        $this->translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->willReturnCallback(function ($key) {
                if ($key === 'general.date.days.4') {
                    return 'vrijdag';
                }
                if ($key === 'general.date.months.3') {
                    return 'maart';
                }
                return $key;
            });

        $result = $this->date_helper->getDisplayDate('2024-03-15 14:30:00');

        $this->assertEquals('vrijdag 15 maart 2024', $result);
    }

    public function testGetDisplayDateWithTime(): void
    {
        $date = new \DateTime('2024-03-15 14:30:45');

        $this->translator
            ->expects($this->exactly(2))
            ->method('trans')
            ->willReturnCallback(function ($key) {
                if ($key === 'general.date.days.4') {
                    return 'vrijdag';
                }
                if ($key === 'general.date.months.3') {
                    return 'maart';
                }
                return $key;
            });

        $result = $this->date_helper->getDisplayDate($date, true);

        $this->assertEquals('vrijdag 15 maart 2024 14:30:45', $result);
    }

    public function testGetDisplayDateShortFormat(): void
    {
        $date = new \DateTime('2024-03-15 14:30:00');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('general.date.monthsShort.3')
            ->willReturn('mrt');

        $result = $this->date_helper->getDisplayDate($date, false, true);

        $this->assertEquals('15 mrt', $result);
    }

    public function testGetDisplayDateShortFormatWithTime(): void
    {
        $date = new \DateTime('2024-03-15 14:30:45');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('general.date.monthsShort.3')
            ->willReturn('mrt');

        $result = $this->date_helper->getDisplayDate($date, true, true);

        $this->assertEquals('15 mrt 14:30:45', $result);
    }
}
