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
            /**
             * @var ForumPostLog[] $forum_logs
             */
            $forum_logs = $this->doctrine->getRepository(ForumPostLog::class)->findBy([], ['id' => 'DESC']);
            foreach ($forum_logs as $forum_log) {
                $this->removeAllWordsForPost($forum_log->post);

                $words = $this->getCleanWordsFromText($forum_log->post->text->text);
                $post_number_in_discussion = $this->forum_discussion_repository->getPostNumberInDiscussion($forum_log->post->discussion, $forum_log->post->id);
                if (0 === $post_number_in_discussion) {
                    // This is the first post in the discussion, we need to include the title
                    $title_words = $this->getCleanWordsFromText($forum_log->post->discussion->title);
                    $this->processWords($title_words, $forum_log->post, true);
                    $this->doctrine->getManager()->flush();

                    $words = \array_diff($words, $title_words);
                }
                $this->processWords($words, $forum_log->post);

                $this->doctrine->getManager()->remove($forum_log);
                $this->doctrine->getManager()->flush();
            }
            $lock->release();
        }

        return 0;
    }

    private function removeAllWordsForPost(ForumPost $post): void
    {
        // Remove all words linked to this post, we will add them below
        foreach ($this->doctrine->getRepository(ForumSearchList::class)->findBy(['post' => $post]) as $forum_search_list) {
            $this->doctrine->getManager()->remove($forum_search_list);
        }
        $this->doctrine->getManager()->flush();
    }

    private function getCleanWordsFromText(string $text): array
    {
        $strange_characters = [
            '^', '$', '&', '(', ')', '<', '>', '`', '\'', '"', '|', ',', '@', '_', '?', '%', '-', '~', '+', '.',
            '[', ']', '{', '}', ':', '\\', '/', '=', '#', '\'', ';', '!', '*'
        ];

        $text = \strip_tags(\strtolower($text));
        // Replace line-endings by spaces
        $text = \str_replace(['<br>', '<br />'], ' ', $text);
        $text = \preg_replace('/[\n\r]/is', ' ', $text);
        // Remove HTML entities
        $text = \preg_replace('/\b&[a-z]+;\b/', ' ', $text);
        // Remove URL's
        $text = \preg_replace('/\b[a-z0-9]+:\/\/[a-z0-9.\-]+(\/[a-z0-9?.%_\-+=&\/]+)?/', ' ', $text);
        // Normalize and filter strange characters such as ^, $, &
        $text = \strtolower($this->normalizeText(\str_replace($strange_characters, ' ', $text)));

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
        }

        return $forum_search_word;
    }

    private function processWords(array $words, ForumPost $post, bool $title = false): void
    {
        foreach ($words as $word) {
            $forum_search_list = new ForumSearchList();
            $forum_search_list->word = $this->getSearchWord($word);
            $forum_search_list->post = $post;
            $forum_search_list->title = $title;

            $this->doctrine->getManager()->persist($forum_search_list);
        }
    }
}
