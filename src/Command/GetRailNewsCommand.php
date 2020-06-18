<?php

namespace App\Command;

use App\Entity\RailNews;
use App\Entity\RailNewsSource;
use App\Entity\RailNewsSourceFeed;
use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use FeedIo\Feed\ItemInterface;
use FeedIo\FeedIo;
use FeedIo\Reader\ReadErrorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetRailNewsCommand extends Command implements ScheduledJobInterface
{
    private const TITLE_ONLY = 'titleOnly';
    private const POSITIVE_WORD = 'positiveWord';
    private const NEGATIVE_WORD = 'negativeWord';

    private static array $wordMatches = [
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' spoor ', self::NEGATIVE_WORD => 'opgespoord'],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' rail ', self::NEGATIVE_WORD => 'vangrail'],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' station ', self::NEGATIVE_WORD => 'tankstation'],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' rails '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' trein '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' machinist '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' conducteur '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' chipkaart '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' ov-chipkaart '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' hispeed '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' HSL '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' fyra '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' syntus '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' noordned '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' veolia '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' acts '],
        [self::TITLE_ONLY => false, self::POSITIVE_WORD => ' railion '],
        [self::TITLE_ONLY => true, self::POSITIVE_WORD => ' NS ', self::NEGATIVE_WORD => 'SNS'],
    ];

    /**
     * @var string
     */
    protected static $defaultName = 'app:get-rail-news';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var FeedIo
     */
    private FeedIo $feedIo;

    /**
     * @param ManagerRegistry $doctrine
     * @param FeedIo $feedIo
     */
    public function __construct(ManagerRegistry $doctrine, FeedIo $feedIo)
    {
        parent::__construct(self::$defaultName);

        $this->doctrine = $doctrine;
        $this->feedIo = $feedIo;
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
        return '14,29,44,59 * * * *';
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Read all the rail-news providers and process the news items');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        /**
         * @var RailNewsSourceFeed[] $feeds
         */
        $feeds = $this->doctrine->getRepository(RailNewsSourceFeed::class)->findAll();
        foreach ($feeds as $feed) {
            /**
             * @var ItemInterface[] $items
             */
            try {
                $items = $this->feedIo->read($feed->url, null, new DateTime('-1 day'))->getFeed();
            } catch (ReadErrorException $exception) {
                $output->writeln($exception->getMessage());
                continue;
            }
            foreach ($items as $item) {
                if ((!$feed->filterResults || $this->isArticleMatch($item)) && !$this->isItemExists($item)) {
                    $this->saveItem($feed->source, $item, $this->getItemDescription($item));
                }
            }
            $this->doctrine->getManager()->flush();
        }

        return 0;
    }

    /**
     * @param ItemInterface $item
     * @return bool
     */
    private function isArticleMatch(ItemInterface $item): bool
    {
        foreach (self::$wordMatches as $wordMatch) {
            if ($this->isWordMatch($wordMatch, $item)) {
                if (isset($wordMatch[self::NEGATIVE_WORD])) {
                    if (stripos($item->getTitle(), $wordMatch[self::NEGATIVE_WORD]) === false
                        && stripos($item->getDescription(), $wordMatch[self::NEGATIVE_WORD]) === false
                    ) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $wordMatch
     * @param ItemInterface $item
     * @return bool
     */
    private function isWordMatch(array $wordMatch, ItemInterface $item): bool
    {
        if ($wordMatch[self::TITLE_ONLY]) {
            return stripos($item->getTitle(), $wordMatch[self::POSITIVE_WORD]) !== false;
        }
        return stripos($item->getTitle(), $wordMatch[self::POSITIVE_WORD]) !== false
            || stripos($item->getDescription(), $wordMatch[self::POSITIVE_WORD]) !== false;
    }

    /**
     * @param ItemInterface $item
     * @return bool
     */
    private function isItemExists(ItemInterface $item): bool
    {
        $railNewsByTitle = $this->doctrine->getRepository(RailNews::class)->findOneBy(
            ['title' => $item->getTitle()]
        );
        return !is_null($railNewsByTitle);
    }

    /**
     * @param ItemInterface $item
     * @return string
     */
    private function getItemDescription(ItemInterface $item): string
    {
        $description = trim(strip_tags(str_replace('?', '', $item->getDescription())));
        $description = preg_replace('/\s\s+/', ' ', $description);
        if (strlen($description) < 1) {
            return $item->getTitle();
        }
        return $description;
    }

    /**
     * @param RailNewsSource $source
     * @param ItemInterface $item
     * @param string $description
     */
    private function saveItem(RailNewsSource $source, ItemInterface $item, string $description): void
    {
        /**
         * @var RailNews $railNews
         */
        $railNews = $this->doctrine->getRepository(RailNews::class)->findOneBy(
            ['url' => $item->getLink()]
        );
        if (is_null($railNews)) {
            $railNews = new RailNews();
            $railNews->title = $item->getTitle();
            $railNews->url = $item->getLink();
            $railNews->introduction = $description;
            $railNews->timestamp = $item->getLastModified() ?? new DateTime();
            $railNews->approved = false;
            $railNews->active = false;
            $railNews->automaticUpdates = true;
            $railNews->source = $source;

            $this->doctrine->getManager()->persist($railNews);
        } elseif ($railNews->automaticUpdates) {
            $railNews->title = $item->getTitle();
            $railNews->url = $item->getLink();
            $railNews->introduction = $description;
            $railNews->timestamp = $item->getLastModified() ?? new DateTime();
        }
    }
}
