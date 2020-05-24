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
    public function sortByFieldFilter(array $content, string $sortBy = null, string $direction = 'asc'): array
    {
        // Unfortunately have to suppress warnings here due to __get function causing usort to think that the array
        // has been modified: "usort(): Array was modified by the user comparison function"
        @usort($content, function ($itemA, $itemB) use ($sortBy, $direction) {
            $flip = ($direction === 'desc') ? -1 : 1;

            $aSortValue = $this->getSortValue($itemA, $sortBy);
            $bSortValue = $this->getSortValue($itemB, $sortBy);
            if ($aSortValue == $bSortValue) {
                return 0;
            } elseif ($aSortValue > $bSortValue) {
                return (1 * $flip);
            } else {
                return (-1 * $flip);
            }
        });
        return $content;
    }

    /**
     * @param object|array $item
     * @param string $sortBy
     * @return string
     */
    private function getSortValue($item, string $sortBy): string
    {
        if (is_array($item)) {
            return $item[$sortBy];
        }
        return $item->$sortBy;
    }
}
