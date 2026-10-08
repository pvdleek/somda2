<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumSearchList;
use App\Entity\ForumSearchWord;
use App\Repository\ForumDiscussionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;

#[AsCommand(
    name: 'app:process-forum-log',
    description: 'Process the forum-log',
    hidden: false,
)]

class ProcessForumLogCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly ForumDiscussionRepository $forum_discussion_repository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);
        $lock = $factory->createLock(self::getName());

        if ($lock->acquire()) {
            /** @var ForumPostLog[] $forum_logs */
            $forum_logs = $this->doctrine->getRepository(ForumPostLog::class)->findBy([], ['id' => 'DESC']);
            $forum_log_ids = \array_map(fn (ForumPostLog $forum_log) => $forum_log->id, $forum_logs);
            $this->doctrine->getManager()->clear();

            foreach ($forum_log_ids as $forum_log_id) {
                try {
                    $this->processForumLog($forum_log_id);
                } catch (\Throwable $exception) {
                    // Log and continue with the next entry, the failed entry stays in the log for the next run
                    $output->writeln(\sprintf(
                        '<error>Failed to process forum-log %d: %s</error>',
                        $forum_log_id,
                        $exception->getMessage()
                    ));
                    $this->doctrine->resetManager();
                }
                $this->doctrine->getManager()->clear();
            }
            $lock->release();
        }

        return 0;
    }

    private function processForumLog(int $forum_log_id): void
    {
        /** @var ForumPostLog|null $forum_log */
        $forum_log = $this->doctrine->getRepository(ForumPostLog::class)->find($forum_log_id);
        if (null === $forum_log) {
            return;
        }

        $this->removeAllWordsForPost($forum_log->post);

        /** @var array<int, bool> $processed_word_ids */
        $processed_word_ids = [];
        $words = $this->getCleanWordsFromText($forum_log->post->text->text);
        $post_number_in_discussion = $this->forum_discussion_repository->getPostNumberInDiscussion($forum_log->post->discussion, $forum_log->post->id);
        if (0 === $post_number_in_discussion) {
            // This is the first post in the discussion, we need to include the title
            $title_words = $this->getCleanWordsFromText($forum_log->post->discussion->title);
            $this->processWords($title_words, $forum_log->post, $processed_word_ids, true);

            $words = \array_diff($words, $title_words);
        }
        $this->processWords($words, $forum_log->post, $processed_word_ids);

        $this->doctrine->getManager()->remove($forum_log);
        $this->doctrine->getManager()->flush();
    }

    private function removeAllWordsForPost(ForumPost $post): void
    {
        // Remove all words linked to this post, we will add them below
        foreach ($this->doctrine->getRepository(ForumSearchList::class)->findBy(['post' => $post]) as $forum_search_list) {
            $this->doctrine->getManager()->remove($forum_search_list);
        }
        $this->doctrine->getManager()->flush();
    }

    /**
     * @return array<string>
     */
    private function getCleanWordsFromText(string $text): array
    {
        $strange_characters = [
            '^', '$', '&', '(', ')', '<', '>', '`', '\'', '"', '|', ',', '@', '_', '?', '%', '-', '~', '+', '.',
            '[', ']', '{', '}', ':', '\\', '/', '=', '#', '\'', ';', '!', '*'
        ];

        $text = \strip_tags(\mb_strtolower($text));
        // Replace line-endings by spaces
        $text = \str_replace(['<br>', '<br />'], ' ', $text);
        $text = \preg_replace('/[\n\r]/is', ' ', $text);
        // Remove HTML entities
        $text = \preg_replace('/\b&[a-z]+;\b/', ' ', $text);
        // Remove URL's
        $text = \preg_replace('/\b[a-z0-9]+:\/\/[a-z0-9.\-]+(\/[a-z0-9?.%_\-+=&\/]+)?/', ' ', $text);
        // Normalize and filter strange characters such as ^, $, &
        $text = \mb_strtolower($this->normalizeText(\str_replace($strange_characters, ' ', $text)));

        return \array_unique(\array_filter(\explode(' ', $text), function ($value) {
            return \strlen($value) > 2 && \strlen($value) <= 50;
        }));
    }

    private function normalizeText(string $text): string
    {
        $table = [
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c',
            'Ć' => 'C', 'ć' => 'c', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
            'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i',
            'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R',
            'ŕ' => 'r',
        ];

        return \strtr($text, $table);
    }

    private function getSearchWord(string $word): ForumSearchWord
    {
        $forum_search_word = $this->doctrine->getRepository(ForumSearchWord::class)->findOneBy(['word' => $word]);
        if (null === $forum_search_word) {
            $forum_search_word = new ForumSearchWord();
            $forum_search_word->word = $word;

            $this->doctrine->getManager()->persist($forum_search_word);
            // Flush immediately, so the word gets an id and will be found by the next lookup
            $this->doctrine->getManager()->flush();
        }

        return $forum_search_word;
    }

    /**
     * @param array<string> $words
     * @param array<int, bool> $processed_word_ids
     */
    private function processWords(array $words, ForumPost $post, array &$processed_word_ids, bool $title = false): void
    {
        foreach ($words as $word) {
            $forum_search_word = $this->getSearchWord($word);
            // Different strings can resolve to the same word because of the case- and accent-insensitive
            // database collation, prevent linking the same word twice to the post
            if (isset($processed_word_ids[\spl_object_id($forum_search_word)])) {
                continue;
            }
            $processed_word_ids[\spl_object_id($forum_search_word)] = true;

            $forum_search_list = new ForumSearchList();
            $forum_search_list->word = $forum_search_word;
            $forum_search_list->post = $post;
            $forum_search_list->title = $title;

            $this->doctrine->getManager()->persist($forum_search_list);
        }
    }
}
