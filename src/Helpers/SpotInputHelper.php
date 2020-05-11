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
     * @var SpotInput
     */
    private SpotInput $spotInput;

    /**
     * @var int
     */
    private int $feedbackFlag = 0;

    /**
     * @var array
     */
    private array $positionArray;

    /**
     * @var TrainNamePattern[]
     */
    private array $trainNamePatterns;

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
        $this->positionArray = $this->doctrine->getRepository(Position::class)->getAllAsArray();
        $this->trainNamePatterns = $this->doctrine
            ->getRepository(TrainNamePattern::class)
            ->findBy([], ['order' => 'ASC']);
        $this->trainTableYear = $this->doctrine->getRepository(TrainTableYear::class)->findTrainTableYearByDate(
            $spotDate
        );
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
            $this->createSpotInputModelFromLine($baseLocation, $spotLine);
            $this->spotInput->spotDate = $spotDate;

            $train = $this->getTrainFromSpotInput();
            $route = $this->getRouteFromSpotInput();

            /**
             * @var Position $position
             */
            $position = $this->doctrine->getRepository(Position::class)->find($this->spotInput->positionId);

            if (!is_null($this->spotInput->location)) {
                // Search for an existing spot
                $existingSpot = $this->doctrine->getRepository(Spot::class)->findOneBy([
                    'spotDate' => $this->spotInput->spotDate,
                    'position' => $position,
                    'location' => $this->spotInput->location,
                    'route' => $route,
                    'train' => $train,
                    'user' => $user,
                ]);
                if (is_null($existingSpot)) {
                    $spot = new Spot();
                    $spot->timestamp = new DateTime();
                    $spot->spotDate = $this->spotInput->spotDate;
                    $spot->train = $train;
                    $train->addSpot($spot);
                    $spot->route = $route;
                    $route->addSpot($spot);
                    $spot->position = $position;
                    $spot->location = $this->spotInput->location;
                    $this->spotInput->location->addSpot($spot);
                    $spot->user = $user;
                    $spot->inputFeedbackFlag = $this->feedbackFlag;
                    if (strlen($this->spotInput->extra) > 0) {
                        $spotExtra = new SpotExtra();
                        $spotExtra->extra = $this->spotInput->extra;
                        $spotExtra->spot = $spot;
                        $spot->extra = $spotExtra;

                        $this->doctrine->getManager()->persist($spotExtra);
                    }
                    $this->doctrine->getManager()->persist($spot);
                    $this->doctrine->getManager()->flush();

                    $spotIdArray[] = $spot->getId();
                } else {
                    $this->flashHelper->add('error', 'Spot op regel ' . ($lineNumber + 1) . ' is al ingevoerd!');
                }
            }
        }

        return $spotIdArray;
    }

    /**
     * @param Location $baseLocation
     * @param string $spotLine
     */
    private function createSpotInputModelFromLine(Location $baseLocation, string $spotLine): void
    {
        $this->feedbackFlag = 0;
        $this->spotInput = new SpotInput();
        $this->spotInput->location = $baseLocation;
        $this->spotInput->positionId = 1;
        $this->spotInput->extra = '';

        $spotPart = explode(' ', $spotLine);

        $this->spotInput->trainNumber = $this->getNextLineItem($spotPart);
        $this->spotInput->routeNumber = $this->getNextLineItem($spotPart);

        if (count($spotPart) > 0) {
            $nextPart = $this->getNextLineItem($spotPart);

            if (in_array(strtoupper($nextPart), $this->positionArray)) {
                // The argument is a position
                $this->spotInput->positionId = array_search(strtoupper($nextPart), $this->positionArray);
                $nextPart = trim(array_shift($spotPart));
            }

            if (!is_null($nextPart) && $this->isLineItemLocation($nextPart)) {
                $nextPart = trim(array_shift($spotPart));
            }

            // The rest of the parts form the extra information
            $this->spotInput->extra = $nextPart . ' ' . implode(' ', $spotPart);
        }
    }

    /**
     * @param array $spotPart
     * @return string
     */
    private function getNextLineItem(array &$spotPart): string
    {
        $item = trim(array_shift($spotPart));
        if ($this->isLineItemLocation($item)) {
            return trim(array_shift($spotPart));
        }
        return $item;
    }

    /**
     * @param string $item
     * @return bool
     */
    private function isLineItemLocation(string $item): bool
    {
        if (substr($item, 0, 1) === '|' && substr($item, -1) === '|') {
            /**
             * @var Location $location
             */
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(
                ['name' => substr($item, 1, strlen($item) - 2)]
            );
            $this->spotInput->location = $location;
            return true;
        }
        return false;
    }

    /**
     * @return Train
     */
    private function getTrainFromSpotInput(): Train
    {
        /**
         * @var Train $train
         */
        $train = $this->doctrine->getRepository(Train::class)->findOneBy(['number' => $this->spotInput->trainNumber]);
        if (!is_null($train)) {
            return $train;
        }

        $train = new Train();
        $train->number = $this->spotInput->trainNumber;

        // Try find a match for the number in all patterns
        foreach ($this->trainNamePatterns as $pattern) {
            if (preg_match('#' . $pattern->pattern . '#', $this->spotInput->trainNumber)) {
                $train->namePattern = $pattern;
                break;
            }
        }

        $this->feedbackFlag += is_null($train->namePattern)
            ? Spot::INPUT_FEEDBACK_TRAIN_NEW_NO_PATTERN : Spot::INPUT_FEEDBACK_TRAIN_NEW;

        $this->doctrine->getManager()->persist($train);

        return $train;
    }

    /**
     * @return Route
     */
    private function getRouteFromSpotInput(): Route
    {
        /**
         * @var Route $route
         */
        $route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $this->spotInput->routeNumber]);
        if (is_null($route)) {
            $route = new Route();
            $route->number = $this->spotInput->routeNumber;

            $this->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NEW;

            $this->doctrine->getManager()->persist($route);

            return $route;
        }

        if (is_null($route->getTrainTableFirstLastByDay($this->spotInput->spotDate->format('N')))) {
            $this->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_DAY;
            return $route;
        }

        $trainTableExists = $this->doctrine->getRepository(TrainTable::class)->isExistingForSpot(
            $this->trainTableYear,
            $route,
            $this->spotInput->location,
            $this->spotInput->spotDate->format('N')
        );
        if (!$trainTableExists) {
            $this->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_LOCATION;
        }

        return $route;
    }
}
