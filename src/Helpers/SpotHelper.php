<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Position;
use App\Entity\Route;
use App\Entity\Spot;
use App\Entity\Train;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class SpotHelper implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Spot $spot
     * @return string
     */
    public function getDisplaySpot(Spot $spot): string
    {
        if (is_numeric($spot->route->number)) {
            $translation = 'spot.display.numeric';
        } elseif ($spot->route->number === Route::SPECIAL_NO_SERVICE) {
            $translation = 'spot.display.noService';
        } elseif (in_array($spot->route->number, Route::SPECIAL_EXTRA_SERVICE)) {
            $translation = 'spot.display.extraService';
        } elseif ($spot->route->number === Route::SPECIAL_MEASURING) {
            $translation = 'spot.display.measuring';
        } elseif ($spot->route->number === Route::SPECIAL_CHECKING) {
            $translation = 'spot.display.checking';
        } else {
            $translation = 'spot.display.numeric';
        }

        return sprintf(
            $this->translator->trans($translation),
            $this->getDisplayTrain($spot->train),
            $this->getDisplayDate($spot->timestamp),
            $this->getDisplayLocation($spot->location),
            $this->getDisplayRoute($spot->route, $spot->position)
        );
    }

    /**
     * @param Train $train
     * @return string
     */
    private function getDisplayTrain(Train $train): string
    {
        if (!is_null($train->namePattern)) {
            return $train->namePattern->name . ' ' . $train->number;
        }
        return $train->number;
    }

    /**
     * @param DateTime $dateTime
     * @return string
     */
    private function getDisplayDate(DateTime $dateTime): string
    {
        if ($dateTime->format(GenericsHelper::DATE_FORMAT_DATABASE) === date(GenericsHelper::DATE_FORMAT_DATABASE)) {
            return $this->translator->trans('general.date.today');
        }
        return $dateTime->format(GenericsHelper::DATE_FORMAT);
    }

    /**
     * @param Location $location
     * @return string
     */
    private function getDisplayLocation(Location $location): string
    {
        return '<span title="' . $location->description . '">' . $location->name . '</span>';
    }

    /**
     * @param Route $route
     * @param Position $position
     * @return string
     */
    private function getDisplayRoute(Route $route, Position $position): string
    {
        if (strlen($position->name) > 0) {
            return $route->number . '(' . $position->name . ')';
        }
        return $route->number;
    }
}
