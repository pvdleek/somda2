<?php

namespace App\Helpers;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DateHelper implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function getDisplayDate(\DateTime|string $date, bool $include_time = false, bool $short_date = false): string
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        $output = '';
        if (!$short_date) {
            $output .= $this->translator->trans('general.date.days.'.($date->format('N') - 1)).' ';
        }
        $output .= $date->format('j').' '.$this->translator->trans('general.date.months'.($short_date ? 'Short' : '').'.'.$date->format('n'));

        if (!$short_date) {
            $output .= ' '.$date->format('Y');
        }
        if ($include_time) {
            $output .= ' '.$date->format('H:i:s');
        }
        return \trim($output);
    }
}
