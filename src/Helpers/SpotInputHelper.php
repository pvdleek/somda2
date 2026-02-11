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
use App\Repository\PositionRepository;
use App\Repository\TrainTableRepository;
use App\Repository\TrainTableYearRepository;
use Doctrine\Persistence\ManagerRegistry;

class SpotInputHelper
{
    private ?TrainTableYear $train_table_year = null;

    private array $position_array = [];

    private array $train_name_patterns = [];

    private bool $initialized = false;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly FlashHelper $flash_helper,
        private readonly TrainTableRepository $train_table_repository,
    ) {
    }

    private function initialize(\DateTime $spot_date): void
    {
        if (!$this->initialized) {
            /** @var PositionRepository $position_repository */
            $position_repository = $this->doctrine->getRepository(Position::class);
            $this->position_array = $position_repository->getAllAsArray();

            $this->train_name_patterns = $this->doctrine->getRepository(TrainNamePattern::class)->findBy([], ['order' => 'ASC']);

            /** @var TrainTableYearRepository $train_table_year_repository */
            $train_table_year_repository = $this->doctrine->getRepository(TrainTableYear::class);
            $this->train_table_year = $train_table_year_repository->findTrainTableYearByDate($spot_date);

            $this->initialized = true;
        }
    }

    /**
     * @return int[]
     */
    public function processSpotLines(array $spot_lines, User $user, \DateTime $spot_date, Location $base_location): array
    {
        $this->initialize($spot_date);

        $spot_id_array = [];

        foreach ($spot_lines as $line_number => $spot_line) {
            $spot_input = $this->createSpotInputModelFromLine($base_location, $spot_line);
            $spot_input->spot_date = $spot_date;
            $spot_input->user = $user;

            if (null !== $spot_id = $this->processSpotInput($spot_input)) {
                $spot_id_array[] = $spot_id;
            } else {
                $this->flash_helper->add('error', 'Spot op regel '.($line_number + 1).' is al ingevoerd!');
            }
        }

        return $spot_id_array;
    }

    public function processSpotInput(SpotInput $spot_input): ?int
    {
        $this->initialize($spot_input->spot_date);

        $train = $this->getTrainFromSpotInput($spot_input);
        $route = $this->getRouteFromSpotInput($spot_input);

        /** @var Position $position */
        $position = $this->doctrine->getRepository(Position::class)->find($spot_input->position_id);

        if (null === $spot_input->location) {
            // We cannot process a spot without a location
            return null;
        }

        if (null === $spot_input->existing_spot_id) {
            // Search for an existing spot
            $existing_spot = $this->doctrine->getRepository(Spot::class)->findOneBy([
                'spot_date' => $spot_input->spot_date,
                'position' => $position,
                'location' => $spot_input->location,
                'route' => $route,
                'train' => $train,
                'user' => $spot_input->user,
            ]);
            if (null !== $existing_spot) {
                return null;
            }

            $spot = new Spot();
            $spot->user = $spot_input->user;
        } else {
            // Update the existing spot
            $spot = $this->doctrine->getRepository(Spot::class)->find($spot_input->existing_spot_id);
            if (null === $spot) {
                return null;
            }
        }

        $spot->timestamp = new \DateTime();
        $spot->spot_date = $spot_input->spot_date;
        $spot->day_number = (int) $spot_input->spot_date->format('N');
        $spot->train = $train;
        $train->addSpot($spot);
        $spot->route = $route;
        $route->addSpot($spot);
        $spot->position = $position;
        $spot->location = $spot_input->location;
        $spot_input->location->addSpot($spot);
        $spot->input_feedback_flag = $spot_input->feedback_flag;
        if (\strlen($spot_input->extra) > 0 || \strlen($spot_input->user_extra) > 0) {
            if (null !== $spot->extra) {
                $spot_extra = $spot->extra;
            } else {
                $spot_extra = new SpotExtra();
                $spot_extra->spot = $spot;
                $this->doctrine->getManager()->persist($spot_extra);
            }
            $spot_extra->extra = $spot_input->extra;
            $spot_extra->user_extra = $spot_input->user_extra ?? '';
            $spot->extra = $spot_extra;
        } elseif (null !== $spot_extra = $spot->extra) {
            $spot->extra = null;
            $this->doctrine->getManager()->remove($spot_extra);
        }

        $this->doctrine->getManager()->persist($spot);
        $this->doctrine->getManager()->flush();

        return $spot->id;
    }

    private function createSpotInputModelFromLine(Location $base_location, string $spot_line): SpotInput
    {
        $spot_input = new SpotInput();
        $spot_input->location = $base_location;
        $spot_input->position_id = 1;
        $spot_input->extra = '';

        $spot_part = \explode(' ', $spot_line);

        $spot_input->train_number = $this->getNextLineItem($spot_part, $spot_input);
        $spot_input->route_number = $this->getNextLineItem($spot_part, $spot_input);

        if (count($spot_part) > 0) {
            $next_part = $this->getNextLineItem($spot_part, $spot_input);

            if (\in_array(\strtoupper($next_part), $this->position_array, true)) {
                // The argument is a position
                $spot_input->position_id = \array_search(\strtoupper($next_part), $this->position_array);
                $next_part = \trim(\array_shift($spot_part));
            }

            if (\strlen($next_part) > 0 && $this->isLineItemLocation($next_part, $spot_input)) {
                $next_part = \trim(\array_shift($spot_part));
            }

            // The rest of the parts form the extra information
            $spot_input->extra = $next_part.' '.\implode(' ', $spot_part);
        }

        return $spot_input;
    }

    private function getNextLineItem(array &$spot_part, SpotInput $spot_input): string
    {
        $item = \trim(\array_shift($spot_part));
        if ($this->isLineItemLocation($item, $spot_input)) {
            return \trim(\array_shift($spot_part));
        }
        return $item;
    }

    private function isLineItemLocation(string $item, SpotInput $spot_input): bool
    {
        if (\substr($item, 0, 1) === '|' && \substr($item, -1) === '|') {
            /** @var Location $location */
            $location = $this->doctrine->getRepository(Location::class)->findOneBy(
                ['name' => \substr($item, 1, \strlen($item) - 2)]
            );
            $spot_input->location = $location;

            return true;
        }
        
        return false;
    }

    private function getTrainFromSpotInput(SpotInput $spot_input): Train
    {
        /** @var Train|null $train */
        $train = $this->doctrine->getRepository(Train::class)->findOneBy(['number' => $spot_input->train_number]);
        if (null !== $train) {
            return $train;
        }

        $train = new Train();
        $train->number = $spot_input->train_number;

        // Try find a match for the number in all patterns
        foreach ($this->train_name_patterns as $pattern) {
            if (\preg_match('#'.$pattern->pattern.'#', $spot_input->train_number)) {
                $train->name_pattern = $pattern;
                break;
            }
        }

        $spot_input->feedback_flag += null === $train->name_pattern ? Spot::INPUT_FEEDBACK_TRAIN_NEW_NO_PATTERN : Spot::INPUT_FEEDBACK_TRAIN_NEW;

        $this->doctrine->getManager()->persist($train);

        return $train;
    }

    private function getRouteFromSpotInput(SpotInput $spot_input): Route
    {
        /** @var Route|null $route */
        $route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $spot_input->route_number]);
        if (null === $route) {
            $route = new Route();
            $route->number = $spot_input->route_number;

            $spot_input->feedback_flag += Spot::INPUT_FEEDBACK_ROUTE_NEW;

            $this->doctrine->getManager()->persist($route);

            return $route;
        }

        if (\is_numeric($route->number)) {
            if (\count($route->getTrainTables()) > 0
                && null === $route->getTrainTableFirstLastByDay($this->train_table_year->id, (int) $spot_input->spot_date->format('N'))
            ) {
                $spot_input->feedback_flag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_DAY;
                return $route;
            }

            $train_table_exists = $this->train_table_repository->isExistingForSpot(
                $this->train_table_year,
                $route,
                $spot_input->location,
                (int) $spot_input->spot_date->format('N')
            );
            if (!$train_table_exists) {
                $spot_input->feedback_flag += Spot::INPUT_FEEDBACK_ROUTE_NOT_ON_LOCATION;
            }
        }

        return $route;
    }
}
