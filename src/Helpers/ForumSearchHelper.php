<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\ForumSearchWord;
use App\Form\ForumSearch;
use App\Model\ForumSearchResult;
use App\Repository\ForumSearchWord as RepositoryForumSearchWord;
use Twig\Extension\RuntimeExtensionInterface;

class ForumSearchHelper implements RuntimeExtensionInterface
{
    public const MAX_RESULTS = 100;

    public function __construct(
        private readonly RepositoryForumSearchWord $repositoryForumSearchWord,
    ) {
    }

    /**
     * @return ForumSearchWord[]
     */
    public function getSearchWords(string $data): array
    {
        $words = \array_filter(\explode(' ', $data));
        foreach ($words as $key => $word) {
            $words[$key] = $this->repositoryForumSearchWord->findOneBy(['word' => $word]);
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
            return $this->repositoryForumSearchWord->searchByWords($searchWords);
        }

        $results = null;
        foreach ($searchWords as $word) {
            $result = $this->repositoryForumSearchWord->searchByWords([$word]);
            if (null === $results) {
                $results = $result;
            } else {
                $results = \array_intersect($results, $result);
            }
        }

        return $results;
    }
}
