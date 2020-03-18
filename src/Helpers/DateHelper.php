<?php

namespace App\Helpers;

use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DateHelper implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param DateTime|string $date
     * @param bool $includeTime
     * @return string
     * @throws Exception
     */
    public function getDisplayDate($date, bool $includeTime = false): string
    {
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }

        $output = $this->translator->trans('general.date.days.' . $date->format('w'));
        $output .= ' ' . $date->format('j');
        $output .= ' ' . $this->translator->trans('general.date.months.' . $date->format('n'));
        $output .= ' ' . $date->format('Y');
        if ($includeTime) {
            $output .= ' ' . $date->format('H:i:s');
        }
        return $output;
    }

    /**
     * @param int $dayNumber
     * @return string
     */
    public function getDayName(int $dayNumber) : string
    {
        switch ($dayNumber) {
            case 0:
                return 'monday';
            case 1:
                return 'tuesday';
            case 2:
                return 'wednesday';
            case 3:
                return 'thursday';
            case 4:
                return 'friday';
            case 5:
                return 'saturday';
            default:
                return 'sunday';
        }
    }

    /**
     * @param string $time
     * @return int
     */
    function timeDisplayToDatabase(string $time) : int
    {
        // Convert the given display time (hh or hh:mm or hh.mm) to a database time (minutes after 2.00)
        $time = str_replace(':', '.', $time);
        if (strlen($time) < 3) {
            $time .= '.00';
        }
        if (strpos($time, '.') === false && strlen($time) == 4) {
            $time = substr($time, 0, 2) . '.' . substr($time, 2, 2);
        }

        $timePart = explode('.', $time);
        $returnTime = $timePart[0] * 60 + $timePart[1] - 120;
        if ($returnTime < 0) {
            return $returnTime + 1440;
        }
        return $returnTime;
    }

    /**
     * @param int $databaseTime
     * @return string
     */
    public function timeDatabaseToDisplay(int $databaseTime) : string
    {
        // Convert the given database time (minutes after 2.00) to a display time
        $databaseTime += 120;
        if ($databaseTime >= 1440) {
            $databaseTime -= 1440;
        }

        $hours = floor($databaseTime / 60);
        $minutes = floor($databaseTime - ($hours * 60));
        return ($hours <= 9 ? '0' : '') . $hours . ':' . ($minutes <= 9 ? '0' : '') . $minutes;
    }
}
