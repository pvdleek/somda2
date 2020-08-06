<?php

namespace App\Helpers;

use App\Entity\Characteristic;
use App\Entity\Location;
use App\Entity\OfficialFootnote;
use App\Entity\OfficialTrainTable;
use App\Entity\Route;
use App\Entity\Transporter;
use App\Model\OfficialRoute;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class OfficialTrainTableHelper
{
    /**
     * @var string
     */
    private string $directory;

    /**
     * @var int
     */
    private int $currentStopNumber = 0;

    /**
     * @var OfficialRoute[]
     */
    private array $routes = [];

    /**
     * @var OfficialFootnote|null
     */
    private ?OfficialFootnote $footnote = null;

    /**
     * @var Characteristic|null
     */
    private ?Characteristic $characteristic = null;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param string $directory
     */
    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * @throws Exception
     */
    public function processFootnotes(): void
    {
        $firstDate = null;
        $handle = fopen($this->directory . '/footnote.dat', 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                switch (substr($line, 0, 1)) {
                    case '@': // Validity
                        $firstDate = DateTime::createFromFormat('dmY', substr($line, 5, 8));
                        break;
                    case '#': // Unique identification, followed by the valid days
                        if (is_null($firstDate)) {
                            throw new Exception('No validity record found before footnote');
                        }
                        $footnoteId = (int)substr($line, 1);
                        $validDays = str_split(fgets($handle), 1);
                        foreach ($validDays as $position => $validDay) {
                            if ($validDay === '1') {
                                $date = clone($firstDate);
                                $date->modify('+' . $position . ' days');

                                $footnote = new OfficialFootnote();
                                $footnote->date = $date;
                                $footnote->footnoteId = $footnoteId;

                                $this->doctrine->getManager()->persist($footnote);
                                $this->doctrine->getManager()->flush();
                                $this->doctrine->getManager()->clear();
                            }
                        }
                        break;
                }
            }
        }
    }

    /**
     *
     */
    public function processCompanies(): void
    {
        $firstDate = null;
        $handle = fopen($this->directory . '/company.dat', 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                switch (substr($line, 0, 1)) {
                    case '@': // Validity
                        break;
                    default: // Company
                        $iffCode = (int)substr($line, 0, 3);
                        $description = trim(substr($line, 15, 30));

                        $transporter = $this->doctrine->getRepository(Transporter::class)->findOneBy(
                            ['iffCode' => $iffCode]
                        );
                        if (is_null($transporter)) {
                            $transporter = new Transporter();
                            $transporter->iffCode = $iffCode;
                            $transporter->name = $description;

                            $this->doctrine->getManager()->persist($transporter);
                            $this->doctrine->getManager()->flush();
                        }

                        break;
                }
            }
        }
    }

    /**
     *
     */
    public function processCharacteristics(): void
    {
        $handle = fopen($this->directory . '/trnsmode.dat', 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                switch (substr($line, 0, 1)) {
                    case '@': // Validity
                        break;
                    default: // Characteristic
                        $name = substr($line, 0, 4);
                        $description = trim(substr($line, 5));

                        $characteristic = $this->doctrine->getRepository(Characteristic::class)->findOneBy(
                            ['name' => $name]
                        );
                        if (is_null($characteristic)) {
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
     * @throws Exception
     */
    public function processTrainTables(): void
    {
        $this->resetForNewRoutes();

        $handle = fopen($this->directory . '/timetbls.dat', 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                switch (substr($line, 0, 1)) {
                    case '#': // Unique identification
                        break;
                    case '%': // Company and route-number
                        $officialRouteModel = new OfficialRoute();
                        $officialRouteModel->transporter = $this->getTransporter((int)substr($line, 1, 3));
                        $officialRouteModel->route = $this->getOrCreateRoute((int)substr($line, 5, 5));
                        $officialRouteModel->firstStopNumber = (int)substr($line, 18, 3);
                        $officialRouteModel->lastStopNumber = (int)substr($line, 22, 3);

                        $this->routes[] = $officialRouteModel;
                        break;
                    case '-': // Footnote
                        $this->footnote = $this->doctrine->getRepository(OfficialFootnote::class)->findOneBy(
                            ['footnoteId' => (int)substr($line, 1, 5)]
                        );
                        break;
                    case '&': // Characteristic
                        $this->characteristic = $this->doctrine->getRepository(Characteristic::class)->findOneBy(
                            ['name' => trim(substr($line, 1, 4))]
                        );
                        break;
                    case '>': // Departure location
                        ++$this->currentStopNumber;
                        $this->saveTrainTable(trim(substr($line, 1, 7)), 'v', substr($line, 9));
                        break;
                    case ';': // Passing location
                        $this->saveTrainTable(trim(substr($line, 1, 7)), '-');
                        break;
                    case '.': // Short stop
                        ++$this->currentStopNumber;
                        $this->saveTrainTable(trim(substr($line, 1, 7)), '+', substr($line, 9));
                        break;
                    case '+': // Long stop
                        ++$this->currentStopNumber;
                        $this->saveTrainTable(trim(substr($line, 1, 7)), 'a', substr($line, 9, 4));
                        $this->saveTrainTable(trim(substr($line, 1, 7)), 'v', substr($line, 14, 4));
                        break;
                    case '<': // Arrival location
                        ++$this->currentStopNumber;
                        $this->saveTrainTable(trim(substr($line, 1, 7)), 'a', substr($line, 9));

                        $this->resetForNewRoutes();
                        break;
                    case '?': // Track information
                        break;
                }
            }

            fclose($handle);
        }
    }

    /**
     *
     */
    private function resetForNewRoutes(): void
    {
        $this->currentStopNumber = 0;

        $this->routes = [];
        $this->footnote = null;
        $this->characteristic = null;

        $this->doctrine->getManager()->clear();
    }

    /**
     * @param int $routeNumber
     * @return Route
     */
    private function getOrCreateRoute(int $routeNumber): Route
    {
        $route = $this->doctrine->getRepository(Route::class)->findOneBy(['number' => $routeNumber]);
        if (is_null($route)) {
            $route = new Route();
            $route->number = $routeNumber;

            $this->doctrine->getManager()->persist($route);
        }

        return $route;
    }

    /**
     * @param int $iffCode
     * @return Transporter
     * @throws Exception
     */
    private function getTransporter(int $iffCode): Transporter
    {
        /**
         * @var Transporter $transporter
         */
        $transporter = $this->doctrine->getRepository(Transporter::class)->findOneBy(['iffCode' => $iffCode]);
        if (is_null($transporter)) {
            throw new Exception('Transport with code ' . $iffCode . ' not found');
        }
        return $transporter;
    }

    /**
     * @param string $locationName
     * @param string $action
     * @param string|null $time
     * @throws Exception
     */
    private function saveTrainTable(string $locationName, string $action, string $time = null): void
    {
        if (is_null($this->footnote)) {
            throw new Exception('Footnote is missing for saving train-table');
        }
        if (is_null($this->characteristic)) {
            throw new Exception('Characteristic is missing for saving train-table');
        }

        $location = $this->doctrine->getRepository(Location::class)->findOneBy(['name' => $locationName]);
        if (is_null($location)) {
            throw new Exception('Location not found when saving train-table');
        }

        foreach ($this->routes as $route) {
            if ($route->firstStopNumber <= $this->currentStopNumber
                && $route->lastStopNumber >= $this->currentStopNumber
            ) {
                $trainTable = new OfficialTrainTable();
                $trainTable->order = $route->order;
                $trainTable->location = $location;
                $trainTable->time = is_null($time) ? null : $trainTable->timeDisplayToDatabase($time);
                $trainTable->action = $action;
                $trainTable->route = $route->route;
                $trainTable->transporter = $route->transporter;
                $trainTable->footnote = $this->footnote;
                $trainTable->characteristic = $this->characteristic;

                $this->doctrine->getManager()->persist($trainTable);
                ++$route->order;
            }
        }

        $this->doctrine->getManager()->flush();
    }
}
