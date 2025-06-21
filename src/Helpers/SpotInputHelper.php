<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Position;
use App\Entity\Route;
use App\Entity\Spot;
use App\Entity\SpotExtra;
use App\Entity\Train;
use App\Entity\TrainNamePattern;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Model\SpotInput;
use App\Repository\Position as RepositoryPosition;
use App\Repository\TrainTable;
use App\Repository\TrainTableYear as RepositoryTrainTableYear;
use Doctrine\Persistence\ManagerRegistry;

class SpotInputHelper
{
    private ?TrainTableYear $trainTableYear = null;

    private array $positionArray = [];

    private array $trainNamePatterns = [];

    private bool $initialized = false;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly FlashHelper $flashHelper,
        private readonly TrainTable $repositoryTrainTable,
    ) {
    }

    private function initialize(\DateTime $spotDate): void
    {
        if (!$this->initialized) {
            /**
             * @var RepositoryPosition $positionRepository
             */
            $positionRepository = $this->doctrine->getRepository(Position::class);
            $this->positionArray = $positionRepository->getAllAsArray();

            $this->trainNamePatterns = $this->doctrine->getRepository(TrainNamePattern::class)->findBy([], ['order' => 'ASC']);

            /**
             * @var RepositoryTrainTableYear $trainTableYearRepository
             */
            $trainTableYearRepository = $this->doctrine->getRepository(TrainTableYear::class);
            $this->trainTableYear = $trainTableYearRepository->findTrainTableYearByDate($spotDate);

            $this->initialized = true;
        }
    }

    /**
     * @return int[]
     */
    public function processSpotLines(array $spotLines, User $user, \DateTime $spotDate, Location $baseLocation): array
    {
        $this->initialize($spotDate);

        $spotIdArray = [];

        foreach ($spotLines as $lineNumber => $spotLine) {
            $spotInput = $this->createSpotInputModelFromLine($baseLocation, $spotLine);
            $spotInput->spotDate = $spotDate;
            $spotInput->user = $user;

            if (null !== $spotId = $this->processSpotInput($spotInput)) {
                $spotIdArray[] = $spotId;
            } else {
                $this->flashHelper->add('error', 'Spot op regel ' . ($lineNumber + 1) . ' is al ingevoerd!');
            }
        }

        return $spotIdArray;
    }

    public function processSpotInput(SpotInput $spotInput): ?int
    {
        $this->initialize($spotInput->spotDate);

        $train = $this->getTrainFromSpotInput($spotInput);
        $route = $this->getRouteFromSpotInput($spotInput);

        /**
         * @var Position $position
         */
        $position = $this->doctrine->getRepository(Position::class)->find($spotInput->positionId);

        if (null === $spotInput->location) {
            // We cannot process a spot without a location
            return null;
        }

        if (null === $spotInput->existingSpotId) {
            // Search for an existing spot
            $existingSpot = $this->doctrine->getRepository(Spot::class)->findOneBy([
                'spotDate' => $spotInput->spotDate,
                'position' => $position,
                'location' => $spotInput->location,
                'route' => $route,
                'train' => $train,
                'user' => $spotInput->user,
            ]);
            if (null !== $existingSpot) {
                return null;
            }

            $spot = new Spot();
            $spot->user = $spotInput->user;
        } else {
            // Update the existing spot
            $spot = $this->doctrine->getRepository(Spot::class)->find($spotInput->existingSpotId);
            if (null === $spot) {
                return null;
            }
        }

        $spot->timestamp = new \DateTime();
        $spot->spotDate = $spotInput->spotDate;
        $spot->dayNumber = (int) $spotInput->spotDate->format('N');
        $spot->train = $train;
        $train->addSpot($spot);
        $spot->route = $route;
        $route->addSpot($spot);
        $spot->position = $position;
        $spot->location = $spotInput->location;
        $spotInput->location->addSpot($spot);
        $spot->inputFeedbackFlag = $spotInput->feedbackFlag;
        if (\strlen($spotInput->extra) > 0 || \strlen($spotInput->userExtra) > 0) {
            if (null !== $spot->extra) {
                $spotExtra = $spot->extra;
            } else {
                $spotExtra = new SpotExtra();
                $spotExtra->spot = $spot;
                $this->doctrine->getManager()->persist($spotExtra);
            }
            $spotExtra->extra = $spotInput->extra;
            $spotExtra->userExtra = $spotInput->userExtra ?? '';
            $spot->extra = $spotExtra;
        } elseif (null !== $spotExtra = $spot->extra) {
            $spot->extra = null;
            $this->doctrine->getManager()->remove($spotExtra);
        }

        $this->doctrine->getManager()->persist($spot);
        $this->doctrine->getManager()->flush();

        return $spot->id;
    }

    private function createSpotInputModelFromLine(Location $baseLocation, string $spotLine): SpotInput
    {
        $spotInput = new SpotInput();
        $spotInput->location = $baseLocation;
        $spotInput->positionId = 1;
        $spotInput->extra = '';

        $spotPart = \explode(' ', $spotLine);

        $spotInput->trainNumber = $this->getNextLineItem($spotPart, $spotInput);
        $spotInput->routeNumber = $this->getNextLineItem($spotPart, $spotInput);

        if (count($spotPart) > 0) {
            $nextPart = $this->getNextLineItem($spotPart, $spotInput);

            if (\in_array(\strtoupper($nextPart), $this->positionArray, true)) {
                // The argument is a position
                $spotInput->positionId = \array_search(\strtoupper($nextPart), $this->positionArray);
                $nextPart = \trim(\array_shift($spotPart));
            }

            if (null !== $nextPart && $this->isLineItemLocation($nextPart, $spotInput)) {
                $nextPart = \trim(\array_shift($spotPart));
            }

            // The rest of the parts form the extra information
            $spotInput->extra = $nextPart . ' ' . \implode(' ', $spotPart);
        }

        return $spotInput;
    }

    private function getNextLineItem(array &$spotPart, SpotInput $spotInput): string
    {
        $item = \trim(\array_shift($spotPart));
        if ($this->isLineItemLocation($item, $spotInput)) {
            return \trim(\array_shift($spotPart));
        }
        return $item;
    }

    private function isLineItemLocation(string $item, SpotInput $spotInput): bool
    {
        if (\substr($item, 0, 1) === '|' && \substr($item, -1) === '|') {
            /**
             * @var Location $location
             */
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(
                ['name' => \substr($item, 1, \strlen($item) - 2)]
            );
            $spotInput->location = $location;
            return true;
        }
        return false;
    }

    private function getTrainFromSpotInput(SpotInput $spotInput): Train
    {
        /**
         * @var Train $train
         */
        $train = $this->doctrine->getRepository(Train::class)->findOneBy(['number' => $spotInput->trainNumber]);
        if (null !== $train) {
            return $train;
        }

        $train = new Train();
        $train->number = $spotInput->trainNumber;

        // Try find a match for the number in all patterns
        foreach ($this->trainNamePatterns as $pattern) {
            if (\preg_match('#' . $pattern->pattern . '#', $spotInput->trainNumber)) {
                $train->namePattern = $pattern;
                break;
            }
        }

        $spotInput->feedbackFlag += null === $train->namePattern
            ? Spot::INPUT_FEEDBACK_TRAIN_NEW_NO_PATTERN : Spot::INPUT_FEEDBACK_TRAIN_NEW;

        $this->doctrine->getManager()->persist($train);

        return $train;
    }

    private function getRouteFromSpotInput(SpotInput $spotInput): Route
    {
        /**
         * @var Route $route
         */
        $route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $spotInput->routeNumber]);
        if (null === $route) {
            $route = new Route();
            $route->number = $spotInput->routeNumber;

            $spotInput->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NEW;

            $this->doctrine->getManager()->persist($route);

            return $route;
        }

        if (\is_numeric($route->number)) {
            if (\count($route->getTrainTables()) > 0
                && null === $route->getTrainTableFirstLastByDay($this->trainTableYear->id, $spotInput->spotDate->format('N'))
            ) {
                $spotInput->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_DAY;
                return $route;
            }

            $trainTableExists = $this->repositoryTrainTable->isExistingForSpot(
                $this->trainTableYear,
                $route,
                $spotInput->location,
                $spotInput->spotDate->format('N')
            );
            if (!$trainTableExists) {
                $spotInput->feedbackFlag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_LOCATION;
            }
        }

        return $route;
    }
}
