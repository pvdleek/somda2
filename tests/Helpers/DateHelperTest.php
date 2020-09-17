<?php declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Helpers\DateHelper;
use App\Tests\BaseTestCase;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateHelperTest extends BaseTestCase
{
    /**
     * @var DateHelper
     */
    private DateHelper $object;

    public function testGetDisplayDate(): void
    {
        $translatorMock = $this->prophet->prophesize(TranslatorInterface::class);
        $translatorMock->trans('general.date.monthsShort.1')->willReturn('jan')->shouldBeCalled();

        $this->object = new DateHelper($translatorMock->reveal());
        $this->assertSame(
            '1 jan',
            $this->object->getDisplayDate(DateTime::createFromFormat('Y-m-d', '2020-01-01'), false, true)
        );
    }

    public function getDisplayDate($date, bool $includeTime = false, bool $shortDate = false): string
    {
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }

        $output = '';
        if (!$shortDate) {
            $output .= $this->translator->trans('general.date.days.' . ($date->format('N') - 1)) . ' ';
        }
        $output .= $date->format('j') . ' ';

        $output .= $this->translator->trans(
            'general.date.months' . ($shortDate ? 'Short' : '') . '.' . $date->format('n')
        );

        if (!$shortDate) {
            $output .= ' ' . $date->format('Y');
        }
        if ($includeTime) {
            $output .= ' ' . $date->format('H:i:s');
        }
        return $output;
    }
}
