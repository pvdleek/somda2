<?php

namespace App\Helpers;

use DateTime;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DateHelper implements RuntimeExtensionInterface
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
     * @param DateTime|string $date
     * @param bool $includeTime
     * @param bool $shortDate
     * @return string
     * @throws Exception
     */
    public function getDisplayDate($date, bool $includeTime = false, bool $shortDate = false): string
    {
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }

        $output = '';
        if (!$shortDate) {
            $output .= $this->translator->trans('general.date.days.' . $date->format('w')) . ' ';
        }
        $output .= $date->format('j');
        $output .= ' ' . $this->translator->trans('general.date.months.' . $date->format('n'));

        if (!$shortDate) {
            $output .= ' ' . $date->format('Y');
        }
        if ($includeTime) {
            $output .= ' ' . $date->format('H:i:s');
        }
        return $output;
    }
}
