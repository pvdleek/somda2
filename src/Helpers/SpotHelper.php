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
    private $translator;

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
        if (is_numeric($spot->getRoute()->getNumber())) {
            $translation = 'spot.display.numeric';
        } elseif ($spot->getRoute()->getNumber() === Route::SPECIAL_NO_SERVICE) {
            $translation = 'spot.display.noService';
        } elseif (in_array($spot->getRoute()->getNumber(), Route::SPECIAL_EXTRA_SERVICE)) {
            $translation = 'spot.display.extraService';
        } elseif ($spot->getRoute()->getNumber() === Route::SPECIAL_MEASURING) {
            $translation = 'spot.display.measuring';
        } elseif ($spot->getRoute()->getNumber() === Route::SPECIAL_CHECKING) {
            $translation = 'spot.display.checking';
        } else {
            $translation = 'spot.display.numeric';
        }

        return sprintf(
            $this->translator->trans($translation),
            $this->getDisplayTrain($spot->getTrain()),
            $this->getDisplayDate($spot->getDate()),
            $this->getDisplayLocation($spot->getLocation()),
            $this->getDisplayRoute($spot->getRoute(), $spot->getPosition())
        );
    }

    /**
     * @param Train $train
     * @return string
     */
    private function getDisplayTrain(Train $train): string
    {
        if (!is_null($train->getNamePattern())) {
            return $train->getNamePattern()->getName() . ' ' . $train->getNumber();
        }
        return $train->getNumber();
    }

    /**
     * @param DateTime $dateTime
     * @return string
     */
    private function getDisplayDate(DateTime $dateTime): string
    {
        if ($dateTime->format('Y-m-d') === date('Y-m-d')) {
            return $this->translator->trans('general.date.today');
        }
        return $dateTime->format('d-m-Y');
    }

    /**
     * @param Location $location
     * @return string
     */
    private function getDisplayLocation(Location $location): string
    {
        return '<span title="' . $location->getDescription() . '">' . $location->getName() . '</span>';
    }

    /**
     * @param Route $route
     * @param Position $position
     * @return string
     */
    private function getDisplayRoute(Route $route, Position $position): string
    {
        if (strlen($position->getName()) > 0) {
            return $route->getNumber() . '(' . $position->getName() . ')';
        }
        return $route->getNumber();
    }
}
