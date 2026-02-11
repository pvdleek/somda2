<?php

namespace App\Helpers;

use App\Entity\Characteristic;
use App\Entity\Location;
use App\Entity\LocationCategory;
use App\Entity\OfficialFootnote;
use App\Entity\OfficialTrainTable;
use App\Entity\Route;
use App\Entity\Transporter;
use App\Model\OfficialRoute;
use Doctrine\Persistence\ManagerRegistry;

class OfficialTrainTableHelper
{
    private string $directory;

    private int $current_stop_number = 0;

    /** @var OfficialRoute[] */
    private array $routes = [];

    private ?OfficialFootnote $footnote = null;

    private ?Characteristic $characteristic = null;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
    }

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * @throws \Exception
     */
    public function processFootnotes(): void
    {
        $first_date = null;
        $handle = \fopen($this->directory.'/footnote.dat', 'r');
        if ($handle) {
            while (($line = \fgets($handle)) !== false) {
                switch (\substr($line, 0, 1)) {
                    case '@': // Validity
                        $first_date = \DateTime::createFromFormat('dmY', substr($line, 5, 8));
                        break;
                    case '#': // Unique identification, followed by the valid days
                        if (null === $first_date) {
                            throw new \Exception('No validity record found before footnote');
                        }
                        $footnote_id = (int) \substr($line, 1);
                        $valid_days = \str_split(\fgets($handle));
                        foreach ($valid_days as $position => $valid_day) {
                            if ($valid_day === '1') {
                                $date = clone($first_date);
                                $date->modify('+'.$position.' days');

                                $footnote = $this->doctrine->getRepository(OfficialFootnote::class)->findOneBy(
                                    ['date' => $date, 'footnote_id' => $footnote_id]
                                );
                                if (null === $footnote) {
                                    $footnote = new OfficialFootnote();
                                    $footnote->date = $date;
                                    $footnote->footnote_id = $footnote_id;

                                    $this->doctrine->getManager()->persist($footnote);
                                }
                            }
                        }
                        $this->doctrine->getManager()->flush();
                        $this->doctrine->getManager()->clear();

                        break;
                }
            }
        }
    }

    public function processCompanies(): void
    {
        $handle = fopen($this->directory.'/company.dat', 'r');
        if ($handle) {
            while (($line = \fgets($handle)) !== false) {
                switch (\substr($line, 0, 1)) {
                    case '@': // Validity
                        break;
                    default: // Company
                        $iff_code = (int) \substr($line, 0, 3);
                        $description = \trim(\substr($line, 15, 30));

                        $transporter = $this->doctrine->getRepository(Transporter::class)->findOneBy(
                            ['iff_code' => $iff_code]
                        );
                        if (null === $transporter) {
                            $transporter = new Transporter();
                            $transporter->iff_code = $iff_code;
                            $transporter->name = $description;

                            $this->doctrine->getManager()->persist($transporter);
                            $this->doctrine->getManager()->flush();
                        }

                        break;
                }
            }
        }
    }

    public function processCharacteristics(): void
    {
        $handle = \fopen($this->directory.'/trnsmode.dat', 'r');
        if ($handle) {
            while (($line = \fgets($handle)) !== false) {
                switch (\substr($line, 0, 1)) {
                    case '@': // Validity
                        break;
                    default: // Characteristic
                        $name = \trim(\substr($line, 0, 4));
                        $description = \trim(\substr($line, 5));

                        $characteristic = $this->doctrine->getRepository(Characteristic::class)->findOneBy(
                            ['name' => $name]
                        );
                        if (null === $characteristic) {
                            $characteristic = new Characteristic();
                            $characteristic->name = $name;
                            $characteristic->description = $description;

                            $this->doctrine->getManager()->persist($characteristic);
                            $this->doctrine->getManager()->flush();
                        }

                        break;
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function processStations(): void
    {
        $handle = \fopen($this->directory.'/stations.dat', 'r');
        if ($handle) {
            while (($line = \fgets($handle)) !== false) {
                switch (\substr($line, 0, 1)) {
                    case '@': // Validity
                        break;
                    default: // Station
                        $abbreviation = \trim(\substr($line, 2, 7));
                        $country_code = \trim(\substr($line, 16, 4));
                        $description = \trim(\substr($line, 43));

                        $location_category = $this->doctrine->getRepository(LocationCategory::class)->findOneBy(
                            ['code' => $country_code]
                        );
                        if (null === $location_category) {
                            throw new \Exception('Country with code '.$country_code.' not found');
                        }

                        $location = $this->doctrine->getRepository(Location::class)->findOneBy(
                            ['name' => $abbreviation]
                        );
                        if (null === $location) {
                            $location = new Location();
                            $location->name = $abbreviation;
                            $location->description = $description;
                            $location->category = $location_category;

                            $this->doctrine->getManager()->persist($location);
                            $this->doctrine->getManager()->flush();
                        }

                        break;
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function processTrainTables(): void
    {
        $this->resetForNewRoutes();

        $handle = \fopen($this->directory.'/timetbls.dat', 'r');
        if ($handle) {
            while (($line = \fgets($handle)) !== false) {
                switch (\substr($line, 0, 1)) {
                    case '#': // Unique identification
                        break;
                    case '%': // Company and route-number
                        $official_route_model = new OfficialRoute();
                        $official_route_model->transporter = $this->getTransporter((int)substr($line, 1, 3));
                        $official_route_model->route = $this->getOrCreateRoute((int)substr($line, 5, 5));
                        $official_route_model->first_stop_number = (int) \substr($line, 18, 3);
                        $official_route_model->last_stop_number = (int) \substr($line, 22, 3);

                        $this->routes[] = $official_route_model;
                        break;
                    case '-': // Footnote
                        $this->footnote = $this->doctrine->getRepository(OfficialFootnote::class)->findOneBy(
                            ['footnote_id$footnote_id' => (int) \substr($line, 1, 5)]
                        );
                        break;
                    case '&': // Characteristic
                        $this->characteristic = $this->doctrine->getRepository(Characteristic::class)->findOneBy(
                            ['name' => \trim(\substr($line, 1, 4))]
                        );
                        break;
                    case '>': // Departure location
                        ++$this->current_stop_number;
                        try {
                            $this->saveTrainTable(trim(substr($line, 1, 7)), 'v', substr($line, 9));
                        } catch (\Exception) {
                            $this->scrollToNextIdentificationLine($handle);
                            $this->resetForNewRoutes();
                        }
                        break;
                    case ';': // Passing location
                        try {
                            $this->saveTrainTable(\trim(\substr($line, 1, 7)), '-');
                        } catch (\Exception) {
                            $this->scrollToNextIdentificationLine($handle);
                            $this->resetForNewRoutes();
                        }
                        break;
                    case '.': // Short stop
                        ++$this->current_stop_number;
                        try {
                            $this->saveTrainTable(\trim(\substr($line, 1, 7)), '+', \substr($line, 9));
                        } catch (\Exception) {
                            $this->scrollToNextIdentificationLine($handle);
                            $this->resetForNewRoutes();
                        }
                        break;
                    case '+': // Long stop
                        ++$this->current_stop_number;
                        try {
                            $this->saveTrainTable(\trim(\substr($line, 1, 7)), 'a', \substr($line, 9, 4));
                            $this->saveTrainTable(\trim(\substr($line, 1, 7)), 'v', \substr($line, 14, 4));
                        } catch (\Exception) {
                            $this->scrollToNextIdentificationLine($handle);
                            $this->resetForNewRoutes();
                        }
                        break;
                    case '<': // Arrival location
                        ++$this->current_stop_number;
                        try {
                            $this->saveTrainTable(\trim(\substr($line, 1, 7)), 'a', \substr($line, 9));
                        } catch (\Exception) {
                            $this->scrollToNextIdentificationLine($handle);
                            $this->resetForNewRoutes();
                        }

                        $this->resetForNewRoutes();
                        break;
                    case '?': // Track information
                        break;
                }
            }

            fclose($handle);
        }
    }

    private function scrollToNextIdentificationLine($handle)
    {
        while (($line = \fgets($handle)) !== false) {
            if (\substr($line, 0, 1) === '#') {
                return;
            }
        }
    }

    private function resetForNewRoutes(): void
    {
        $this->current_stop_number = 0;

        $this->routes = [];
        $this->footnote = null;
        $this->characteristic = null;

        $this->doctrine->getManager()->clear();
    }

    private function getOrCreateRoute(int $route_number): Route
    {
        $route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $route_number]);
        if (null === $route) {
            $route = new Route();
            $route->number = (string) $route_number;

            $this->doctrine->getManager()->persist($route);
        }

        return $route;
    }

    /**
     * @throws \Exception
     */
    private function getTransporter(int $iff_code): Transporter
    {
        /** @var Transporter|null $transporter */
        $transporter = $this->doctrine->getRepository(Transporter::class)->findOneBy(['iff_code' => $iff_code]);
        if (null === $transporter) {
            throw new \Exception('Transport with code '.$iff_code.' not found');
        }
        return $transporter;
    }

    /**
     * @throws \Exception
     */
    private function saveTrainTable(string $location_name, string $action, ?string $time = null): void
    {
        if (null === $this->footnote) {
            throw new \Exception(
                'Footnote is missing for saving train-table, location '.$location_name.', action '. $action .
                ', time '.$time.', first route '.$this->routes[0]->route->number
            );
        }
        if (null === $this->characteristic) {
            throw new \Exception(
                'Characteristic is missing for saving train-table, location '.$location_name.', action '. $action .
                ', time '.$time.', first route '.$this->routes[0]->route->number
            );
        }

        $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $location_name]);
        if (null === $location) {
            throw new \Exception(
                'Location not found when saving train-table, location '.$location_name.', action '. $action .
                ', time '.$time.', first route '.$this->routes[0]->route->number
            );
        }

        foreach ($this->routes as $route) {
            if ($route->first_stop_number <= $this->current_stop_number
                && $route->last_stop_number >= $this->current_stop_number
            ) {
                $train_table = new OfficialTrainTable();
                $train_table->order = $route->order;
                $train_table->location = $location;
                $train_table->time = null === $time ? null : $train_table->timeDisplayToDatabase(trim($time));
                $train_table->action = $action;
                $train_table->route = $route->route;
                $train_table->transporter = $route->transporter;
                $train_table->footnote = $this->footnote;
                $train_table->characteristic = $this->characteristic;

                $this->doctrine->getManager()->persist($train_table);
                $this->doctrine->getManager()->flush();

                ++$route->order;
            }
        }
    }
}
