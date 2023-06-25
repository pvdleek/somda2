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
    public function getDisplayDate(\DateTime|string $date, bool $includeTime = false, bool $shortDate = false): string
    {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        $output = '';
        if (!$shortDate) {
            $output .= $this->translator->trans('general.date.days.' . ($date->format('N') - 1)) . ' ';
        }
        $output .= $date->format('j') . ' ';

        $output .= $this->translator->trans(
            'general.date.months' . ($shortDate ? 'Short' : '') . '.' . $date->format('n')
        );

        if (!$shortDate) {
            $output .= ' ' . $date->format('Y');
        }
        if ($includeTime) {
            $output .= ' ' . $date->format('H:i:s');
        }
        return \trim($output);
    }
}
