<?php
declare(strict_types=1);

namespace App\Tests\Traits;

use App\Traits\DateTrait;
use PHPUnit\Framework\TestCase;

class DateTraitTest extends TestCase
{
    private object $trait_object;

    protected function setUp(): void
    {
        $this->trait_object = new class {
            use DateTrait;
        };
    }

    // timeDisplayToDatabase tests

    public function testTimeDisplayToDatabaseWithColonFormat(): void
    {
        // 14:30 = 14.5 hours after midnight = 870 minutes - 120 = 750 minutes after 2:00 AM
        $result = $this->trait_object->timeDisplayToDatabase('14:30');
        
        $this->assertEquals(750, $result);
    }

    public function testTimeDisplayToDatabaseWithDotFormat(): void
    {
        // 14.30 should work the same as 14:30
        $result = $this->trait_object->timeDisplayToDatabase('14.30');
        
        $this->assertEquals(750, $result);
    }

    public function testTimeDisplayToDatabaseWithHourOnly(): void
    {
        // 14 = 14:00 = 840 minutes - 120 = 720 minutes after 2:00 AM
        $result = $this->trait_object->timeDisplayToDatabase('14');
        
        $this->assertEquals(720, $result);
    }

    public function testTimeDisplayToDatabaseWithFourDigitsNoSeparator(): void
    {
        // 1430 should be interpreted as 14:30
        $result = $this->trait_object->timeDisplayToDatabase('1430');
        
        $this->assertEquals(750, $result);
    }

    public function testTimeDisplayToDatabaseAfterMidnight(): void
    {
        // 01:00 = 60 minutes - 120 = -60, which wraps to 1380 (1440 - 60)
        $result = $this->trait_object->timeDisplayToDatabase('01:00');
        
        $this->assertEquals(1380, $result);
    }

    public function testTimeDisplayToDatabaseAtTwoAM(): void
    {
        // 02:00 = 120 minutes - 120 = 0 minutes after 2:00 AM
        $result = $this->trait_object->timeDisplayToDatabase('02:00');
        
        $this->assertEquals(0, $result);
    }

    public function testTimeDisplayToDatabaseAtMidnight(): void
    {
        // 00:00 = 0 minutes - 120 = -120, which wraps to 1320 (1440 - 120)
        $result = $this->trait_object->timeDisplayToDatabase('00:00');
        
        $this->assertEquals(1320, $result);
    }

    public function testTimeDisplayToDatabaseMorning(): void
    {
        // 08:00 = 480 minutes - 120 = 360 minutes after 2:00 AM
        $result = $this->trait_object->timeDisplayToDatabase('08:00');
        
        $this->assertEquals(360, $result);
    }

    public function testTimeDisplayToDatabaseNoon(): void
    {
        // 12:00 = 720 minutes - 120 = 600 minutes after 2:00 AM
        $result = $this->trait_object->timeDisplayToDatabase('12:00');
        
        $this->assertEquals(600, $result);
    }

    // timeDatabaseToDisplay tests

    public function testTimeDatabaseToDisplayAfternoon(): void
    {
        // 750 minutes after 2:00 AM = 750 + 120 = 870 minutes = 14:30
        $result = $this->trait_object->timeDatabaseToDisplay(750);
        
        $this->assertEquals('14:30', $result);
    }

    public function testTimeDatabaseToDisplayMorning(): void
    {
        // 360 minutes after 2:00 AM = 360 + 120 = 480 minutes = 08:00
        $result = $this->trait_object->timeDatabaseToDisplay(360);
        
        $this->assertEquals('08:00', $result);
    }

    public function testTimeDatabaseToDisplayAfterMidnight(): void
    {
        // -60 minutes after 2:00 AM = -60 + 120 = 60 minutes = 01:00
        $result = $this->trait_object->timeDatabaseToDisplay(-60);
        
        $this->assertEquals('01:00', $result);
    }

    public function testTimeDatabaseToDisplayAtTwoAM(): void
    {
        // 0 minutes after 2:00 AM = 0 + 120 = 120 minutes = 02:00
        $result = $this->trait_object->timeDatabaseToDisplay(0);
        
        $this->assertEquals('02:00', $result);
    }

    public function testTimeDatabaseToDisplayLateEvening(): void
    {
        // 1320 minutes after 2:00 AM = 1320 + 120 = 1440, wraps to 0 = 00:00
        $result = $this->trait_object->timeDatabaseToDisplay(1320);
        
        $this->assertEquals('00:00', $result);
    }

    public function testTimeDatabaseToDisplayWithPadding(): void
    {
        // 10 minutes after 2:00 AM = 10 + 120 = 130 minutes = 02:10
        $result = $this->trait_object->timeDatabaseToDisplay(10);
        
        $this->assertEquals('02:10', $result);
    }

    public function testTimeDatabaseToDisplaySingleDigitHourAndMinute(): void
    {
        // 370 minutes after 2:00 AM = 370 + 120 = 490 minutes = 08:10
        $result = $this->trait_object->timeDatabaseToDisplay(370);
        
        $this->assertEquals('08:10', $result);
    }

    // Round trip tests

    public function testRoundTripConversionAfternoon(): void
    {
        $original = '14:30';
        $database = $this->trait_object->timeDisplayToDatabase($original);
        $converted = $this->trait_object->timeDatabaseToDisplay($database);
        
        $this->assertEquals($original, $converted);
    }

    public function testRoundTripConversionMorning(): void
    {
        $original = '08:15';
        $database = $this->trait_object->timeDisplayToDatabase($original);
        $converted = $this->trait_object->timeDatabaseToDisplay($database);
        
        $this->assertEquals($original, $converted);
    }

    public function testRoundTripConversionAfterMidnight(): void
    {
        $original = '01:45';
        $database = $this->trait_object->timeDisplayToDatabase($original);
        $converted = $this->trait_object->timeDatabaseToDisplay($database);
        
        $this->assertEquals($original, $converted);
    }

    // getDayName tests

    public function testGetDayNameMonday(): void
    {
        $result = $this->trait_object->getDayName(0);
        
        $this->assertEquals('monday', $result);
    }

    public function testGetDayNameTuesday(): void
    {
        $result = $this->trait_object->getDayName(1);
        
        $this->assertEquals('tuesday', $result);
    }

    public function testGetDayNameWednesday(): void
    {
        $result = $this->trait_object->getDayName(2);
        
        $this->assertEquals('wednesday', $result);
    }

    public function testGetDayNameThursday(): void
    {
        $result = $this->trait_object->getDayName(3);
        
        $this->assertEquals('thursday', $result);
    }

    public function testGetDayNameFriday(): void
    {
        $result = $this->trait_object->getDayName(4);
        
        $this->assertEquals('friday', $result);
    }

    public function testGetDayNameSaturday(): void
    {
        $result = $this->trait_object->getDayName(5);
        
        $this->assertEquals('saturday', $result);
    }

    public function testGetDayNameSunday(): void
    {
        $result = $this->trait_object->getDayName(6);
        
        $this->assertEquals('sunday', $result);
    }

    public function testGetDayNameDefaultToSunday(): void
    {
        // Any value not 0-5 should return sunday
        $result = $this->trait_object->getDayName(7);
        
        $this->assertEquals('sunday', $result);
    }

    public function testGetDayNameNegativeDefaultToSunday(): void
    {
        // Negative values should also return sunday
        $result = $this->trait_object->getDayName(-1);
        
        $this->assertEquals('sunday', $result);
    }
}
