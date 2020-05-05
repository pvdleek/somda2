<?php

namespace App\Command;

use App\Entity\ForumPost;
use App\Entity\ForumPostLog;
use App\Entity\ForumSearchList;
use App\Entity\ForumSearchWord;
use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessForumLogCommand extends Command implements ScheduledJobInterface
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:process-forum-log';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct(self::$defaultName);

        $this->doctrine = $doctrine;
    }

    /**
     *
     */
    public function __invoke()
    {
        $this->execute();
    }

    /**
     * @return string
     */
    public function getSchedulerExpresion(): string
    {
        return '4,19,34,49 * * * *';
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Process the forum-log');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        /**
         * @var ForumPostLog[] $forumLogs
         */
        $forumLogs = $this->doctrine->getRepository(ForumPostLog::class)->findAll();
        foreach ($forumLogs as $forumLog) {
            if ($forumLog->action === ForumPostLog::ACTION_POST_EDIT) {
                $this->removeAllWordsForPost($forumLog->post);
            }

            $words = $this->getCleanWordsFromText($forumLog->post->text->text);
            $this->processWords($words, $forumLog->post);

            $this->doctrine->getManager()->flush();

            $postNrInDiscussion = $this->doctrine
                ->getRepository('App:ForumDiscussion')
                ->getPostNumberInDiscussion($forumLog->post->discussion, $forumLog->post->getId());
            if ($postNrInDiscussion === 0) {
                // This is the first post in the discussion, we need to include the title
                $words = $this->getCleanWordsFromText($forumLog->post->discussion->title);
                $this->processWords($words, $forumLog->post, true);
            }

            $this->doctrine->getManager()->remove($forumLog);

            $this->doctrine->getManager()->flush();
        }

        return 0;
    }

    /**
     * @param ForumPost $post
     */
    private function removeAllWordsForPost(ForumPost $post): void
    {
        // Remove all words linked to this post, we will add them below
        $forumSearchLists = $this->doctrine->getRepository(ForumSearchList::class)->findBy(['post' => $post]);
        foreach ($forumSearchLists as $forumSearchList) {
            $this->doctrine->getManager()->remove($forumSearchList);
        }
        $this->doctrine->getManager()->flush();
    }

    /**
     * @param string $text
     * @return array
     */
    private function getCleanWordsFromText(string $text): array
    {
        $strangeCharacters = [
            '^', '$', '&', '(', ')', '<', '>', '`', '\'', '"', '|', ',', '@', '_', '?', '%', '-', '~', '+', '.',
            '[', ']', '{', '}', ':', '\\', '/', '=', '#', '\'', ';', '!', '*'
        ];

        $text = strip_tags(strtolower($text));
        // Replace line-endings by spaces
        $text = preg_replace('/[\n\r]/is', ' ', $text);
        // Remove HTML entities
        $text = preg_replace('/\b&[a-z]+;\b/', ' ', $text);
        // Remove URL's
        $text = preg_replace('/\b[a-z0-9]+:\/\/[a-z0-9.\-]+(\/[a-z0-9?.%_\-+=&\/]+)?/', ' ', $text);
        // Filter strange characters such as ^, $, &
        $text = str_replace($strangeCharacters, ' ', $text);

        return array_unique(array_filter(explode(' ', $text), function($value) {
            return strlen($value) > 2;
        }));
    }

    /**
     * @param string $word
     * @return ForumSearchWord
     */
    private function getSearchWord(string $word): ForumSearchWord
    {
        $forumSearchWord = $this->doctrine->getRepository(ForumSearchWord::class)->findOneBy(['word' => $word]);
        if (is_null($forumSearchWord)) {
            $forumSearchWord = new ForumSearchWord();
            $forumSearchWord->word = $word;

            $this->doctrine->getManager()->persist($forumSearchWord);
        }

        return $forumSearchWord;
    }

    /**
     * @param array $words
     * @param ForumPost $post
     * @param bool $title
     */
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
