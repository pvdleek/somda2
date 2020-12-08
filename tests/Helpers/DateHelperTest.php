<?php declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\DateHelper;
use App\Tests\BaseTestCase;
use DateTime;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateHelperTest extends BaseTestCase
{
    /**
     * @var DateHelper
     */
    private DateHelper $object;

    /**
     * @return array
     */
    public function getDisplayDateProvider(): array
    {
        return [
            ['Tuesday 1 january 2020', DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 15:16:17'), false, false],
            [
                'Tuesday 1 january 2020 15:16:17',
                DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 15:16:17'),
                true,
                false
            ],
            ['1 jan', DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 15:16:17'), false, true],
            ['1 jan 15:16:17', DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 15:16:17'), true, true],
        ];
    }

    /**
     * @param string $expectedOutput
     * @param DateTime $dateTime
     * @param bool $includeTime
     * @param bool $shortDate
     * @dataProvider getDisplayDateProvider
     * @throws Exception
     */
    public function testGetDisplayDate(
        string $expectedOutput,
        DateTime $dateTime,
        bool $includeTime,
        bool $shortDate
    ): void {
        $translatorMock = $this->prophet->prophesize(TranslatorInterface::class);
        $translatorMock->trans('general.date.days.2')->willReturn('Tuesday');
        $translatorMock->trans('general.date.monthsShort.1')->willReturn('jan');
        $translatorMock->trans('general.date.months.1')->willReturn('january');

        $this->object = new DateHelper($translatorMock->reveal());
        $this->assertSame($expectedOutput, $this->object->getDisplayDate($dateTime, $includeTime, $shortDate));
    }
}
