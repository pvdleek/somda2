<?php

namespace App\Helpers;

use Twig\Extension\RuntimeExtensionInterface;

class SortHelper implements RuntimeExtensionInterface
{
    /**
     * @param array $content
     * @param string|null $sortBy
     * @param string $direction
     * @return array
     */
    public function sortByFieldFilter(array $content, string $sortBy = null, string $direction = 'asc') {
        // Unfortunately have to suppress warnings here due to __get function causing usort to think that the array
        // has been modified: "usort(): Array was modified by the user comparison function"
        @usort($content, function ($a, $b) use ($sortBy, $direction) {
            $flip = ($direction === 'desc') ? -1 : 1;

            $aSortValue = $this->getSortValue($a, $sortBy);
            $bSortValue = $this->getSortValue($b, $sortBy);
            if ($aSortValue == $bSortValue) {
                return 0;
            } else if ($aSortValue > $bSortValue) {
                return (1 * $flip);
            } else {
                return (-1 * $flip);
            }
        });
        return $content;
    }

    /**
     * @param array $array
     * @param string $sortBy
     * @return string
     */
    private function getSortValue(array $array, string $sortBy) : string
    {
        if (is_array($array)) {
            return $array[$sortBy];
        } else {
            return $array->{'get' . ucfirst($sortBy)}();
        }
    }
}
