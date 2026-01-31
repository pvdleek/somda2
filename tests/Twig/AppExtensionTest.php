<?php
declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

class AppExtensionTest extends TestCase
{
    private AppExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new AppExtension();
    }

    public function testGetFilters(): void
    {
        $filters = $this->extension->getFilters();

        $this->assertIsArray($filters);
        $this->assertNotEmpty($filters);
        
        $filter_names = array_map(fn(TwigFilter $filter) => $filter->getName(), $filters);
        
        $this->assertContains('chr', $filter_names);
        $this->assertContains('displayUser', $filter_names);
        $this->assertContains('displayDateTime', $filter_names);
        $this->assertContains('displayTime', $filter_names);
        $this->assertContains('displayForumPost', $filter_names);
        $this->assertContains('displaySpot', $filter_names);
        $this->assertContains('displayRouteOperationDays', $filter_names);
        $this->assertContains('fileTimestamp', $filter_names);
    }

    public function testFileTimestampFilterReturnsString(): void
    {
        // Create a temporary test file
        $test_file = 'test_file_for_timestamp.txt';
        $full_path = __DIR__ . '/../../html/' . $test_file;
        
        if (!file_exists(dirname($full_path))) {
            $this->markTestSkipped('html directory does not exist');
        }
        
        // If we can't create files, skip
        if (!is_writable(dirname($full_path))) {
            $this->markTestSkipped('html directory is not writable');
        }
        
        file_put_contents($full_path, 'test');
        
        $result = $this->extension->fileTimestampFilter($test_file);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertMatchesRegularExpression('/^\d+$/', $result);
        
        // Clean up
        unlink($full_path);
    }

    public function testTimeDatabaseToDisplay(): void
    {
        // Test conversion from minutes after 2:00 AM to display time
        $result = $this->extension->timeDatabaseToDisplay(750);
        
        $this->assertEquals('14:30', $result);
    }

    public function testTimeDatabaseToDisplayMorning(): void
    {
        // Test morning time (e.g., 8:00 AM = 6 hours after 2 AM = 360 minutes)
        $result = $this->extension->timeDatabaseToDisplay(360);
        
        $this->assertEquals('08:00', $result);
    }

    public function testTimeDatabaseToDisplayAfterMidnight(): void
    {
        // Test time after midnight but before 2 AM (e.g., 1:00 AM = -60 minutes)
        $result = $this->extension->timeDatabaseToDisplay(-60);
        
        $this->assertEquals('01:00', $result);
    }

    public function testExtendsAbstractExtension(): void
    {
        $this->assertInstanceOf(\Twig\Extension\AbstractExtension::class, $this->extension);
    }
}
