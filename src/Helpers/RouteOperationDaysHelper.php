<?php

namespace App\Helpers;

use App\Entity\RouteOperationDays;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class RouteOperationDaysHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getDisplay(RouteOperationDays $route_operation_days, bool $short = false): string
    {
        $days_array = [];
        for ($day = 0; $day < 7; ++$day) {
            if ($route_operation_days->isRunningOnDay($day)) {
                $days_array[] = \strtolower(
                    $this->translator->trans('general.date.days'.($short ? 'Short' : '').'.'.$day)
                );
            }
        }

        return \ucfirst(\implode(', ', $days_array));
    }
}
