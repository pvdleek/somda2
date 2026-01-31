<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\ForumSearchWord;
use App\Form\ForumSearch;
use App\Model\ForumSearchResult;
use App\Repository\ForumSearchWordRepository;
use Twig\Extension\RuntimeExtensionInterface;

class ForumSearchHelper implements RuntimeExtensionInterface
{
    public const MAX_RESULTS = 100;

    public function __construct(
        private readonly ForumSearchWordRepository $forum_search_word_repository,
    ) {
    }

    /**
     * @return ForumSearchWord[]
     */
    public function getSearchWords(string $data): array
    {
        $words = \array_filter(\explode(' ', $data));
        foreach ($words as $key => $word) {
            $words[$key] = $this->forum_search_word_repository->findOneBy(['word' => $word]);
        }
        return $words;
    }

    /**
     * @param ForumSearchWord[] $search_words
     * @return ForumSearchResult[]
     */
    public function getSearchResults(string $search_method, array $search_words): array
    {
        if ($search_method === ForumSearch::METHOD_SOME) {
            return $this->forum_search_word_repository->searchByWords($search_words);
        }

        $results = null;
        foreach ($search_words as $word) {
            $result = $this->forum_search_word_repository->searchByWords([$word]);
            if (null === $results) {
                $results = $result;
            } else {
                $results = \array_intersect($results, $result);
            }
        }

        return $results;
    }
}
