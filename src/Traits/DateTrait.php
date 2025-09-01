<?php
declare(strict_types=1);

namespace App\Traits;

trait DateTrait
{
    public function timeDisplayToDatabase(string $time): int
    {
        // Convert the given display time (hh or hh:mm or hh.mm) to a database time (minutes after 2.00)
        $time = \str_replace(':', '.', $time);
        if (\strlen($time) < 3) {
            $time .= '.00';
        }
        if (\strpos($time, '.') === false && \strlen($time) === 4) {
            $time = \substr($time, 0, 2) . '.' . \substr($time, 2, 2);
        }

        $timePart = \explode('.', $time);
        $returnTime = (int) $timePart[0] * 60 + (int) $timePart[1] - 120;
        if ($returnTime < 0) {
            return $returnTime + 1440;
        }
        return $returnTime;
    }

    public function timeDatabaseToDisplay(int $databaseTime): string
    {
        // Convert the given database time (minutes after 2.00) to a display time
        $databaseTime += 120;
        if ($databaseTime >= 1440) {
            $databaseTime -= 1440;
        }

        $hours = \floor($databaseTime / 60);
        $minutes = \floor($databaseTime - ($hours * 60));
        return ($hours <= 9 ? '0' : '') . $hours . ':' . ($minutes <= 9 ? '0' : '') . $minutes;
    }

    public function getDayName(int $day_number): string
    {
        switch ($day_number) {
            case 0:
                $day = 'monday';
                break;
            case 1:
                $day = 'tuesday';
                break;
            case 2:
                $day = 'wednesday';
                break;
            case 3:
                $day = 'thursday';
                break;
            case 4:
                $day = 'friday';
                break;
            case 5:
                $day = 'saturday';
                break;
            default:
                $day = 'sunday';
                break;
        }
        return $day;
    }
}
