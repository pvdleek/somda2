<?php

namespace App\Helpers;

use App\Entity\Location;
use App\Entity\Position;
use App\Entity\Route;
use App\Entity\Spot;
use App\Entity\Train;
use App\Generics\DateGenerics;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class SpotHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getDisplaySpot(Spot $spot, bool $noHtml = false): string
    {
        if (\is_numeric($spot->route->number)) {
            $translation = 'spot.display.numeric';
        } elseif ($spot->route->number === Route::SPECIAL_NO_SERVICE) {
            $translation = 'spot.display.noService';
        } elseif (\in_array($spot->route->number, Route::SPECIAL_EXTRA_SERVICE)) {
            $translation = 'spot.display.extraService';
        } elseif ($spot->route->number === Route::SPECIAL_MEASURING) {
            $translation = 'spot.display.measuring';
        } elseif ($spot->route->number === Route::SPECIAL_CHECKING) {
            $translation = 'spot.display.checking';
        } else {
            $translation = 'spot.display.numeric';
        }

        return \sprintf(
            $this->translator->trans($translation),
            $this->getDisplayTrain($spot->train),
            $this->getDisplayDate($spot->spotDate),
            $this->getDisplayLocation($spot->location, $noHtml),
            $this->getDisplayRoute($spot->route, $spot->position)
        );
    }

    private function getDisplayTrain(Train $train): string
    {
        if (null !== $train->namePattern) {
            return $train->namePattern->name . ' ' . $train->number;
        }
        return $train->number;
    }

    private function getDisplayDate(\DateTime $dateTime): string
    {
        if ($dateTime->format(DateGenerics::DATE_FORMAT_DATABASE) === date(DateGenerics::DATE_FORMAT_DATABASE)) {
            return $this->translator->trans('general.date.today');
        }
        return $dateTime->format(DateGenerics::DATE_FORMAT);
    }

    private function getDisplayLocation(Location $location, bool $noHtml): string
    {
        if ($noHtml) {
            return $location->name;
        }
        return '<span title="' . $location->description . '">' . $location->name . '</span>';
    }

    private function getDisplayRoute(Route $route, Position $position): string
    {
        if (\strlen($position->name) > 0) {
            return $route->number . '(' . $position->name . ')';
        }
        return $route->number;
    }
}
