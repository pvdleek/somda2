<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\ForumSearchWord;
use App\Form\ForumSearch;
use App\Model\ForumSearchResult;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\RuntimeExtensionInterface;

class ForumSearchHelper implements RuntimeExtensionInterface
{
    public const MAX_RESULTS = 100;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
    }

    /**
     * @return ForumSearchWord[]
     */
    public function getSearchWords(string $data): array
    {
        $words = \array_filter(\explode(' ', $data));
        foreach ($words as $key => $word) {
            $words[$key] = $this->doctrine->getRepository(ForumSearchWord::class)->findOneBy(
                ['word' => $word]
            );
        }
        return $words;
    }

    /**
     * @param ForumSearchWord[] $searchWords
     * @return ForumSearchResult[]
     */
    public function getSearchResults(string $searchMethod, array $searchWords): array
    {
        if ($searchMethod === ForumSearch::METHOD_SOME) {
            return $this->doctrine->getRepository(ForumSearchWord::class)->searchByWords($searchWords);
        }

        $results = null;
        foreach ($searchWords as $word) {
            $result = $this->doctrine->getRepository(ForumSearchWord::class)->searchByWords([$word]);
            if (\is_null($results)) {
                $results = $result;
            } else {
                $results = \array_intersect($results, $result);
            }
        }

        return $results;
    }
}
