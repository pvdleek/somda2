<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\Location;
use App\Entity\Position;
use App\Entity\Route;
use App\Entity\Spot;
use App\Entity\Train;
use App\Entity\TrainNamePattern;
use App\Helpers\SpotHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpotHelperTest extends TestCase
{
    private SpotHelper $spot_helper;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->spot_helper = new SpotHelper($this->translator);
    }

    public function testGetDisplaySpotWithNumericRoute(): void
    {
        $spot = $this->createSpot('1234', 'IC 1234', 'Amsterdam Centraal', 'Amsterdam CS');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.numeric')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot);

        $this->assertStringContainsString('IC 1234', $result);
        $this->assertStringContainsString('Amsterdam CS', $result);
    }

    public function testGetDisplaySpotWithNoService(): void
    {
        $spot = $this->createSpot(Route::SPECIAL_NO_SERVICE, 'Test Train', 'Utrecht', 'Utrecht CS');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.noService')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot);

        $this->assertIsString($result);
    }

    public function testGetDisplaySpotWithExtraService(): void
    {
        $spot = $this->createSpot(Route::SPECIAL_EXTRA_SERVICE[0], 'Test Train', 'Rotterdam', 'Rotterdam CS');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.extraService')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot);

        $this->assertIsString($result);
    }

    public function testGetDisplaySpotWithMeasuring(): void
    {
        $spot = $this->createSpot(Route::SPECIAL_MEASURING, 'Test Train', 'Den Haag', 'Den Haag HS');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.measuring')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot);

        $this->assertIsString($result);
    }

    public function testGetDisplaySpotWithChecking(): void
    {
        $spot = $this->createSpot(Route::SPECIAL_CHECKING, 'Test Train', 'Eindhoven', 'Eindhoven CS');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.checking')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot);

        $this->assertIsString($result);
    }

    public function testGetDisplaySpotWithTrainNamePattern(): void
    {
        $train_name_pattern = $this->createMock(TrainNamePattern::class);
        $train_name_pattern->name = 'ICE';

        $train = $this->createMock(Train::class);
        $train->number = '123';
        $train->name_pattern = $train_name_pattern;

        $spot = $this->createSpot('500', '', 'Test Location', 'Test Desc');
        $spot->train = $train;

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.numeric')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot);

        $this->assertStringContainsString('ICE 123', $result);
    }

    public function testGetDisplaySpotWithPositionName(): void
    {
        $spot = $this->createSpot('1234', 'Test Train', 'Test Location', 'Test Desc', 'A');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.numeric')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot);

        $this->assertStringContainsString('1234(A)', $result);
    }

    public function testGetDisplaySpotNoHtml(): void
    {
        $spot = $this->createSpot('1234', 'Test Train', 'Amsterdam', 'Amsterdam Centraal');

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('spot.display.numeric')
            ->willReturn('Train %s on %s at %s as %s');

        $result = $this->spot_helper->getDisplaySpot($spot, true);

        // Should not contain HTML tags when no_html is true
        $this->assertStringNotContainsString('<span', $result);
        $this->assertStringContainsString('Amsterdam', $result);
    }

    private function createSpot(
        string $route_number,
        string $train_number,
        string $location_name,
        string $location_description,
        string $position_name = ''
    ): Spot {
        $route = $this->createMock(Route::class);
        $route->number = $route_number;

        $train = $this->createMock(Train::class);
        $train->number = $train_number;
        $train->name_pattern = null;

        $location = $this->createMock(Location::class);
        $location->name = $location_name;
        $location->description = $location_description;

        $position = $this->createMock(Position::class);
        $position->name = $position_name;

        $spot = $this->createMock(Spot::class);
        $spot->route = $route;
        $spot->train = $train;
        $spot->location = $location;
        $spot->position = $position;
        $spot->spot_date = new \DateTime('2024-03-15');

        return $spot;
    }
}
