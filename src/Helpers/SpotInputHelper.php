<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Position;
use App\Entity\Route;
use App\Entity\Spot;
use App\Entity\SpotExtra;
use App\Entity\Train;
use App\Entity\TrainNamePattern;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Model\SpotInput;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

class SpotInputHelper
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FlashHelper
     */
    private FlashHelper $flashHelper;

    /**
     * @var TrainTableYear
     */
    private TrainTableYear $trainTableYear;

    /**
     * @var array
     */
    private array $positionArray;

    /**
     * @var TrainNamePattern[]
     */
    private array $trainNamePatterns;

    /**
     * @var bool
     */
    private bool $initialized = false;

    /**
     * @param ManagerRegistry $doctrine
     * @param FlashHelper $flashHelper
     */
    public function __construct(ManagerRegistry $doctrine, FlashHelper $flashHelper)
    {
        $this->doctrine = $doctrine;
        $this->flashHelper = $flashHelper;
    }

    /**
     * @param DateTime $spotDate
     */
    private function initialize(DateTime $spotDate): void
    {
        if (!$this->initialized) {
            $this->positionArray = $this->doctrine->getRepository(Position::class)->getAllAsArray();
            $this->trainNamePatterns = $this->doctrine
                ->getRepository(TrainNamePattern::class)
                ->findBy([], ['order' => 'ASC']);
            $this->trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->findTrainTableYearByDate(
                $spotDate
            );

            $this->initialized = true;
        }
    }

    /**
     * @param array $spotLines
     * @param User $user
     * @param DateTime $spotDate
     * @param Location $baseLocation
     * @return int[]
     */
    public function processSpotLines(array $spotLines, User $user, DateTime $spotDate, Location $baseLocation): array
    {
        $this->initialize($spotDate);

        $spotIdArray = [];

        foreach ($spotLines as $lineNumber => $spotLine) {
            $spotInput = $this->createSpotInputModelFromLine($baseLocation, $spotLine);
            $spotInput->spotDate = $spotDate;
            $spotInput->user = $user;

            if (!is_null($spotId = $this->processSpotInput($spotInput))) {
                $spotIdArray[] = $spotId;
            } else {
                $this->flashHelper->add('error', 'Spot op regel ' . ($lineNumber + 1) . ' is al ingevoerd!');
            }
        }

        return $spotIdArray;
    }

    /**
     * @param SpotInput $spotInput
     * @return int|null
     */
    public function processSpotInput(SpotInput $spotInput): ?int
    {
        $this->initialize($spotInput->spotDate);

        $train = $this->getTrainFromSpotInput($spotInput);
        $route = $this->getRouteFromSpotInput($spotInput);

        /**
         * @var Position $position
         */
        $position = $this->doctrine->getRepository(Position::class)->find($spotInput->positionId);

        if (is_null($spotInput->location)) {
            // We cannot process a spot without a location
            return null;
        }

        if (is_null($spotInput->existingSpotId)) {
            // Search for an existing spot
            $existingSpot = $this->doctrine->getRepository(Spot::class)->findOneBy([
                'spotDate' => $spotInput->spotDate,
                'position' => $position,
                'location' => $spotInput->location,
                'route' => $route,
                'train' => $train,
                'user' => $spotInput->user,
            ]);
            if (!is_null($existingSpot)) {
                return null;
            }

            $spot = new Spot();
            $spot->user = $spotInput->user;
        } else {
            // Update the existing spot
            $spot = $this->doctrine->getRepository(Spot::class)->find($spotInput->existingSpotId);
            if (is_null($spot)) {
                return null;
            }
        }

        $spot->timestamp = new DateTime();
        $spot->spotDate = $spotInput->spotDate;
        $spot->train = $train;
        $train->addSpot($spot);
        $spot->route = $route;
        $route->addSpot($spot);
        $spot->position = $position;
        $spot->location = $spotInput->location;
        $spotInput->location->addSpot($spot);
        $spot->inputFeedbackFlag = $spotInput->feedbackFlag;
        if (strlen($spotInput->extra) > 0 || strlen($spotInput->userExtra) > 0) {
            if (!is_null($spot->extra)) {
                $spotExtra = $spot->extra;
            } else {
                $spotExtra = new SpotExtra();
                $this->doctrine->getManager()->persist($spotExtra);
            }
            $spotExtra->extra = $spotInput->extra;
            $spotExtra->userExtra = $spotInput->userExtra ?? '';
            $spotExtra->spot = $spot;
            $spot->extra = $spotExtra;
        }

        $this->doctrine->getManager()->persist($spot);
        $this->doctrine->getManager()->flush();

        return $spot->getId();
    }

    /**
     * @param Location $baseLocation
     * @param string $spotLine
     * @return SpotInput
     */
    private function createSpotInputModelFromLine(Location $baseLocation, string $spotLine): SpotInput
    {
        $spotInput = new SpotInput();
        $spotInput->location = $baseLocation;
        $spotInput->positionId = 1;
        $spotInput->extra = '';

        $spotPart = explode(' ', $spotLine);

        $spotInput->trainNumber = $this->getNextLineItem($spotPart, $spotInput);
        $spotInput->routeNumber = $this->getNextLineItem($spotPart, $spotInput);

        if (count($spotPart) > 0) {
            $nextPart = $this->getNextLineItem($spotPart, $spotInput);

            if (in_array(strtoupper($nextPart), $this->positionArray)) {
                // The argument is a position
                $spotInput->positionId = array_search(strtoupper($nextPart), $this->positionArray);
                $nextPart = trim(array_shift($spotPart));
            }

            if (!is_null($nextPart) && $this->isLineItemLocation($nextPart, $spotInput)) {
                $nextPart = trim(array_shift($spotPart));
            }

            // The rest of the parts form the extra information
            $spotInput->extra = $nextPart . ' ' . implode(' ', $spotPart);
        }

        return $spotInput;
    }

    /**
     * @param array $spotPart
     * @param SpotInput $spotInput
     * @return string
     */
    private function getNextLineItem(array &$spotPart, SpotInput $spotInput): string
    {
        $item = trim(array_shift($spotPart));
        if ($this->isLineItemLocation($item, $spotInput)) {
            return trim(array_shift($spotPart));
        }
        return $item;
    }

    /**
     * @param string $item
     * @param SpotInput $spotInput
     * @return bool
     */
    private function isLineItemLocation(string $item, SpotInput $spotInput): bool
    {
        if (substr($item, 0, 1) === '|' && substr($item, -1) === '|') {
            /**
             * @var Location $location
             */
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(
                ['name' => substr($item, 1, strlen($item) - 2)]
            );
            $spotInput->location = $location;
            return true;
        }
        return false;
    }

    /**
     * @param SpotInput $spotInput
     * @return Train
     */
    private function getTrainFromSpotInput(SpotInput $spotInput): Train
    {
        /**
         * @var Train $train
         */
        $train = $this->doctrine->getRepository(Train::class)->findOneBy(['number' => $spotInput->trainNumber]);
        if (!is_null($train)) {
            return $train;
        }

        $train = new Train();
        $train->number = $spotInput->trainNumber;

        // Try find a match for the number in all patterns
        foreach ($this->trainNamePatterns as $pattern) {
            if (preg_match('#' . $pattern->pattern . '#', $spotInput->trainNumber)) {
                $train->namePattern = $pattern;
                break;
            }
        }

        $spotInput->feedbackFlag += is_null($train->namePattern)
            ? Spot::INPUT_FEEDBACK_TRAIN_NEW_NO_PATTERN : Spot::INPUT_FEEDBACK_TRAIN_NEW;

        $this->doctrine->getManager()->persist($train);

        return $train;
    }

    /**
     * @param SpotInput $spotInput
     * @return Route
     */
    private function getRouteFromSpotInput(SpotInput $spotInput): Route
    {
        /**
         * @var Route $route
         */
        $route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $spotInput->routeNumber]);
        if (is_null($route)) {
            $route = new Route();
            $route->number = $spotInput->routeNumber;

            $spotInput->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NEW;

            $this->doctrine->getManager()->persist($route);

            return $route;
        }

        if (is_null($route->getTrainTableFirstLastByDay($spotInput->spotDate->format('N')))) {
            $spotInput->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_DAY;
            return $route;
        }

        $trainTableExists = $this->doctrine->getRepository(TrainTable::class)->isExistingForSpot(
            $this->trainTableYear,
            $route,
            $spotInput->location,
            $spotInput->spotDate->format('N')
        );
        if (!$trainTableExists) {
            $spotInput->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_LOCATION;
        }

        return $route;
    }
}
