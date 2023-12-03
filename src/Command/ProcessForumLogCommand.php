<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumSearchList;
use App\Entity\ForumSearchWord;
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
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);
        $lock = $factory->createLock(self::getDefaultName());

        if ($lock->acquire()) {
            /**
             * @var ForumPostLog[] $forumLogs
             */
            $forumLogs = $this->doctrine->getRepository(ForumPostLog::class)->findBy([], ['id' => 'DESC']);
            foreach ($forumLogs as $forumLog) {
                $this->removeAllWordsForPost($forumLog->post);

                $words = $this->getCleanWordsFromText($forumLog->post->text->text);
                $postNrInDiscussion = $this->doctrine
                    ->getRepository(ForumDiscussion::class)
                    ->getPostNumberInDiscussion($forumLog->post->discussion, $forumLog->post->id);
                if ($postNrInDiscussion === 0) {
                    // This is the first post in the discussion, we need to include the title
                    $titleWords = $this->getCleanWordsFromText($forumLog->post->discussion->title);
                    $this->processWords($titleWords, $forumLog->post, true);
                    $this->doctrine->getManager()->flush();

                    $words = \array_diff($words, $titleWords);
                }
                $this->processWords($words, $forumLog->post);

                $this->doctrine->getManager()->remove($forumLog);
                $this->doctrine->getManager()->flush();
            }
            $lock->release();
        }

        return 0;
    }

    private function removeAllWordsForPost(ForumPost $post): void
    {
        // Remove all words linked to this post, we will add them below
        $forumSearchLists = $this->doctrine->getRepository(ForumSearchList::class)->findBy(['post' => $post]);
        foreach ($forumSearchLists as $forumSearchList) {
            $this->doctrine->getManager()->remove($forumSearchList);
        }
        $this->doctrine->getManager()->flush();
    }

    private function getCleanWordsFromText(string $text): array
    {
        $strangeCharacters = [
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
        $text = \strtolower($this->normalizeText(\str_replace($strangeCharacters, ' ', $text)));

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
        $forumSearchWord = $this->doctrine->getRepository(ForumSearchWord::class)->findOneBy(['word' => $word]);
        if (null === $forumSearchWord) {
            $forumSearchWord = new ForumSearchWord();
            $forumSearchWord->word = $word;

            $this->doctrine->getManager()->persist($forumSearchWord);
        }

        return $forumSearchWord;
    }

    private function processWords(array $words, ForumPost $post, bool $title = false): void
    {
        foreach ($words as $word) {
            $forumSearchList = new ForumSearchList();
            $forumSearchList->word = $this->getSearchWord($word);
            $forumSearchList->post = $post;
            $forumSearchList->title = $title;

            $this->doctrine->getManager()->persist($forumSearchList);
        }
    }
}
