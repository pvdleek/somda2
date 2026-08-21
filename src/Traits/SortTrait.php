<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\ForumForum;
use App\Entity\Route;

trait SortTrait
{
    /**
     * @param array<int|string, Route|ForumForum|array<string, mixed>> $content
     * @return array<int|string, Route|ForumForum|array<string, mixed>>
     */
    public function sortByFieldFilter(array $content, ?string $sort_by = null, string $direction = 'asc'): array
    {
        // Unfortunately have to suppress warnings here due to __get function causing usort to think that the array
        // has been modified: "usort(): Array was modified by the user comparison function"
        @\usort($content, function ($item_a, $item_b) use ($sort_by, $direction) {
            $flip = ($direction === 'desc') ? -1 : 1;

            $a_sort_value = $this->getSortValue($item_a, $sort_by);
            $b_sort_value = $this->getSortValue($item_b, $sort_by);
            if ($a_sort_value == $b_sort_value) {
                return 0;
            } elseif ($a_sort_value > $b_sort_value) {
                return (1 * $flip);
            } else {
                return (-1 * $flip);
            }
        });

        return $content;
    }

    /**
     * @param object|Route|ForumForum|array<string, mixed> $item
     */
    private function getSortValue($item, string $sort_by): string
    {
        if (\is_array($item)) {
            return (string) $item[$sort_by];
        }

        return (string) $item->$sort_by;
    }
}
