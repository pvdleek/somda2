<?php

namespace App\Helpers;

use App\Entity\RouteOperationDays;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class RouteOperationDaysHelper implements RuntimeExtensionInterface
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
     * @param RouteOperationDays $routeOperationDays
     * @param bool $short
     * @return string
     */
    public function getDisplay(RouteOperationDays $routeOperationDays, bool $short = false): string
    {
        $daysArray = [];
        for ($day = 0; $day < 7; ++$day) {
            if ($routeOperationDays->isRunningOnDay($day)) {
                $daysArray[] = strtolower(
                    $this->translator->trans('general.date.days' . ($short ? 'Short' : '') . '.' . $day)
                );
            }
        }

        return ucfirst(implode(', ', $daysArray));
    }
}
